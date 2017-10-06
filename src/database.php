<?php

require_once(__DIR__.'/lib/querybuilder.php');

class YEdDatabase
{
	private $host;
	private $name;
	private $user;
	private $pass;
	private $mysqli;
	private $last_error;
	
	const status_recyclebin 		= 'recyclebin';
	const status_draft 				= 'draft';	
	const status_all 				= 'all';			// pseudo status
	const status_not_recyclebin 	= 'notrecyclebin';	// pseudo status
	
	const status_passed = 'passed';
	const status_failed = 'failed';
	const status_idle 	= 'idle';
	
	public function __construct($db_host, $db_name, $db_user, $db_pass) 
	{
		$this->host 		= $db_host;
		$this->name 		= $db_name;
		$this->user 		= $db_user;
		$this->pass 		= $db_pass;
		$this->mysqli 		= NULL;
		$this->last_error 	= 0;
		
		$this->Connect();
	}
	
	public function __destruct() 
	{
		if ( $this->mysqli != NULL )
		{
			$this->mysqli->close();
			$this->mysqli = NULL;
		}
	}
	
	private function Connect() 
	{		
		// Create a new mysqli object with database connection parameters
		$this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->name);
		if($this->mysqli->connect_errno) 
		{
			$this->last_error 	= $this->mysqli->connect_errno;
			$this->mysqli 		= NULL;
			return False;
		}
		return True;
	}
	
	private static function utf8_encode_deep(&$input) 
	{
		if (is_string($input)) {
			$input = utf8_encode($input);
		} else if (is_array($input)) {
			foreach ($input as &$value) {
				self::utf8_encode_deep($value);
			}
			unset($value);
		} else if (is_object($input)) {
			$vars = array_keys(get_object_vars($input));
			foreach ($vars as $var) {
				self::utf8_encode_deep($input->$var);
			}
		}
	}
	
	public function escape_string($str){
		return $this->mysqli->real_escape_string($str);	
	}
	
	//========================================================== 
	// PUBLIC part
	
	public function IsConnected() {
		return $this->mysqli != NULL;
	}
	
	public function LastError() {
		return $this->last_error;
	}
	
	public function Execute(QueryBuilder $queryobj)
	{
		$query 	= $queryobj->build();
		$stmt 	= $this->mysqli->query($query);
		$results = array();
		while (is_object($stmt) && $result = $stmt->fetch_assoc()) {
			$results[] = $result;	
		}
		if (is_object($stmt)) $stmt->close();		
		return $results;
	}
	
	public function ExecuteQuery($query)
	{
		$stmt = $this->mysqli->prepare($query);
		if($stmt->execute()) {
			return True;
		}
		return False;
	}
	
	//==================================================
	
	public function GetFiles() 
	{
		$stmt = $this->mysqli->prepare("SELECT vf.id, vf.name, vf.imports, vf.modified, vf.created, count(r.id) as count 
				FROM virtual_file vf 
				LEFT JOIN rule r on r.file_id = vf.id AND r.status<> \"" . self::status_recyclebin . "\" 
				GROUP BY vf.id, vf.name, vf.imports, vf.modified, vf.created");
		$stmt->execute();
		$stmt->bind_result($id, $name, $imports, $modified, $created, $count);
		$results = array();
		while ($stmt->fetch()) {
			$imports_exploded = array_filter(explode(",", $imports));	
			$results[] = array('id' => $id, 'name' => $name, 'imports' => $imports_exploded, 'rules' => $count, 'last_modified' => $modified, 'created' => $created);
		}
		$stmt->close();		
		return $results;
	}
	
	public function GetFile($file_id) 
	{
		$stmt = $this->mysqli->prepare("SELECT vf.id, vf.name, vf.imports, vf.modified, vf.created, count(r.id) as count 
				FROM virtual_file vf 
				LEFT JOIN rule r on r.file_id = vf.id
				WHERE vf.id = ?
				GROUP BY vf.id");
		$stmt->bind_param("i", $file_id);
		$stmt->execute();
		$stmt->bind_result($id, $name, $imports, $modified, $created, $count);
		$results = array();
		if ($stmt->fetch()) {
			$imports_exploded = array_filter(explode(",", $imports));	
			$results = array('id' => $id, 'name' => $name, 'imports' => $imports_exploded, 'rules' => $count, 'last_modified' => $modified, 'created' => $created);
		}
		$stmt->close();		
		return $results;
	}
	
	public function FileExists($file_name)
	{
		$stmt = $this->mysqli->prepare("SELECT count(*) as count FROM virtual_file WHERE name = ?");
		$stmt->bind_param("s", $file_name);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();		
		return ((int) $count) > 0;
	}
	
	public function AddFile($file_name, $imports) 
	{
		$join_imports = implode(",", $imports);
		$stmt = $this->mysqli->prepare("INSERT INTO virtual_file (name, imports, created, modified) VALUES (?, ?, NOW(), NOW())");
		$stmt->bind_param("ss", $file_name, $join_imports);
		$stmt->execute();
		$id = $this->mysqli->insert_id;
		$stmt->close();		
		return $id;
	}
	
	public function CopyFile($file_id) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO virtual_file (name, imports, created, modified) SELECT name, imports, NOW(), NOW() FROM virtual_file WHERE id=?");
		$stmt->bind_param("i", $file_id);
		$stmt->execute();
		$id = $this->mysqli->insert_id;
		$stmt->close();		
		return $id;
	}
	
	public function UpdateFile($file_id, $file_name, $imports) 
	{
		$join_imports = implode(",", $imports);
		$stmt = $this->mysqli->prepare("UPDATE virtual_file SET name=?, imports=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("ssi", $file_name, $join_imports, $file_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function RenameFile($file_id, $file_name) 
	{
		$stmt = $this->mysqli->prepare("UPDATE virtual_file SET name=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("si", $file_name, $file_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function MarkFileAsUpdated($file_id) 
	{
		$stmt = $this->mysqli->prepare("UPDATE virtual_file SET modified=NOW() WHERE id=?");
		$stmt->bind_param("i", $file_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function DeleteFile($file_id) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM virtual_file WHERE id=?");
		$stmt->bind_param("i", $file_id);
		$stmt->execute();		
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function DeleteFileMetas($file_id) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM virtual_file_metas WHERE file_id=?");
		$stmt->bind_param("i", $file_id);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	public function CopyFileMetas($file_id, $file_copy_id) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO virtual_file_metas (file_id, name, value) SELECT ?, name, value FROM virtual_file_metas WHERE file_id=?");
		$stmt->bind_param("ii", $file_copy_id, $file_id);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	public function GetRulesCount($file_id) 
	{
		$stmt = $this->mysqli->prepare("SELECT count(*) as count FROM rule WHERE file_id=? AND status<>?");
		$stmt->bind_param($file_id, self::status_recyclebin);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();		
		return (int) $count;
	}
	
	public function GetRules($file_id = -1, $limit = -1, $status = self::status_not_recyclebin, $user = -1) 
	{
		$queryobj = new QueryBuilder();
		$table_rule = new QueryTable('rule');
		$table_rule->setSelect(array(
				'id' => 'id',
				'file_id' => 'file_id',
				'name' => 'name',
				'cond' => 'cond',
				'is_private' => 'is_private',
				'is_global' => 'is_global',
				'tags' => 'tags',
				'modified' => 'last_modified',
				'created' => 'created',
				'status' => 'status'
		));
		$table_rule->setRawSelect(array(
				"(SELECT f.name FROM virtual_file f WHERE f.id = rule.file_id)" => "file_name",
				"(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = '__threat')" => "threat",
				"(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = '__author')" => "author_id"
		));
		$table_rule->addGroupBy('id');
		$table_rule->addOrderBy(new QueryOrderBy('modified', 'DESC', True));		
		
		if ( $user != -1 ) {
			$table_rule->addWhere(new QueryWhere('author_id', $this->escape_string($user), '=', 'int'));
		}
		if ( $file_id != -1 ) {
			$table_rule->addWhere(new QueryWhere('file_id', $this->escape_string($file_id), '=', 'int'));
		}
		if ( $status == self::status_recyclebin ) {
			$table_rule->addWhere(new QueryWhere('status', self::status_recyclebin, '=', 'text'));
		}
		else if ( $status == self::status_not_recyclebin ) {
			$table_rule->addWhere(new QueryWhere('status', self::status_recyclebin, '<>', 'text'));
		}
		if ( $limit != -1 ) {
			$queryobj->setLimits(0, $this->escape_string($limit));
		}
		
		$queryobj->addTable($table_rule);
		$results = $this->Execute($queryobj);
		foreach($results as &$result)
		{
			$result["tags"] = array_filter(explode(',', $result["tags"]));
		}
		return $results;
	}
	
	public function SearchRules($params) 
	{
		$queryobj = new QueryBuilder();
		
		$table_rule = new QueryTable('rule');
		$table_rule->setSelect(array(
				'id' => 'id',
				'file_id' => 'file_id',
				'name' => 'name',
				'cond' => 'cond',
				'is_private' => 'is_private',
				'is_global' => 'is_global',
				'tags' => 'tags',
				'modified' => 'last_modified',
				'created' => 'created',
				'status' => 'status'
		));
		$table_rule->setRawSelect(array(
				"(SELECT f.name FROM virtual_file f WHERE f.id = rule.file_id)" => "file_name",
				"(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = '__threat')" => "threat",
				"(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = '__author')" => "author_id"
		));
		$table_rule->addGroupBy('id');
		$table_rule->addOrderBy(new QueryOrderBy('name', 'ASC', True));		
		
		// Filters: Quick search can only be used alone.
		if ( isset($params->quick) && $params->quick != -1 ) 
		{
			$table_rule->setWhereCondition('OR');
			$filter_statement = new QueryWhere();
			$filter_statement->addChildren(new QueryWhere('name', '%' . $this->escape_string($params->quick) . '%', 'LIKE', 'str', 'OR'));			
			$filter_statement->addChildren(new QueryWhere('FIND_IN_SET("' . $this->escape_string($params->quick) . '", tags)', '', '', '', 'OR'));	
			$filter_statement->addChildren(new QueryWhere('(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = "__threat") LIKE "%' . $this->escape_string($params->quick) . '%"', '', '', '', 'OR'));
			$filter_statement->addChildren(new QueryWhere('(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = "__comment") LIKE "%' . $this->escape_string($params->quick) . '%"', '', '', '', 'OR'));
			$filter_statement->addChildren(new QueryWhere('(meta.name LIKE "%' . $this->escape_string($params->quick) . '%" OR meta.value LIKE "%' . $this->escape_string($params->quick) . '%")', '', '', '', 'OR'));			
			$filter_statement->addChildren(new QueryWhere('(string.name LIKE "%' . $this->escape_string($params->quick) . '%" OR string.value LIKE "%' . $this->escape_string($params->quick) . '%")', '', '', '', 'OR'));
			
			$table_metas = new QueryTable('meta');	
			$table_metas->setWhereCondition('OR');
			$table_metas->addJoinWhere(new QueryWhere('rule_id', 'rule.id', '=', 'field'));
			$table_metas->setJoinType('LEFT');
			$queryobj->addJoinTable($table_metas);
						
			$table_strings = new QueryTable('string');	
			$table_strings->setWhereCondition('OR');
			$table_strings->addJoinWhere(new QueryWhere('rule_id', 'rule.id', '=', 'field'));
			$table_strings->setJoinType('LEFT');
			$queryobj->addJoinTable($table_strings);
			
			$filter_statement->addChildren(new QueryWhere('cond', '%' . $this->escape_string($params->quick) . '%', 'LIKE', 'str', 'OR'));
			$table_rule->addWhere($filter_statement);			
			$table_rule->addWhere(new QueryWhere('status', self::status_recyclebin, '<>', 'text', 'AND'));
		}
		else 
		{
			if ( isset($params->file) && $params->file != -1 ) {
				$table_rule->addWhere(new QueryWhere('file_id', $this->escape_string($params->file), '=', 'int'));
			}
			if ( isset($params->is_private) && $params->is_private != -1 ) {
				if ( $params->is_private == "true" )
					$table_rule->addWhere(new QueryWhere('is_private', 0, '>', 'int'));
				else
					$table_rule->addWhere(new QueryWhere('is_private', 0, '=', 'int'));
			}
			if ( isset($params->is_global) && $params->is_global != -1 ) {
				if ( $params->is_global == "true" )
					$table_rule->addWhere(new QueryWhere('is_global', 0, '>', 'int'));
				else
					$table_rule->addWhere(new QueryWhere('is_global', 0, '=', 'int'));
			}
			if ( isset($params->name) && $params->name != -1 ) {
				$table_rule->addWhere(new QueryWhere('name', '%' . $this->escape_string($params->name) . '%', 'LIKE', 'str'));
			}
			if ( isset($params->tags) && $params->tags != -1 ) {
				$table_rule->addRawWhere('FIND_IN_SET("' . $this->escape_string($params->tags) . '", tags)');
			}
			if ( isset($params->author) && $params->author != -1 ) {
				$table_rule->addRawWhere('(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = "__author") = ' . $this->escape_string($params->author));
			}	
			if ( isset($params->threat) && $params->threat != -1 ) {
				$table_rule->addRawWhere('(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = "__threat") LIKE "%' . $this->escape_string($params->threat) . '%"');
			}
			if ( isset($params->comment) && $params->comment != -1 ) {
				$table_rule->addRawWhere('(SELECT m.value FROM rule_metas m WHERE m.rule_id = rule.id AND m.name = "__comment") LIKE "%' . $this->escape_string($params->comment) . '%"');
			}
			if ( isset($params->metas) && $params->metas != -1 ) {
				$table_metas = new QueryTable('meta');	
				$table_metas->addRawWhere('(meta.name LIKE "%' . $this->escape_string($params->metas) . '%" OR meta.value LIKE "%' . $this->escape_string($params->metas) . '%")');
				$table_metas->addJoinWhere(new QueryWhere('rule_id', 'rule.id', '=', 'field'));
				$table_metas->setJoinType('LEFT');
				$queryobj->addJoinTable($table_metas);
			}
			if ( isset($params->strings) && $params->strings != -1 ) {
				$table_strings = new QueryTable('string');	
				$table_strings->addRawWhere('(string.name LIKE "%' . $this->escape_string($params->strings) . '%" OR string.value LIKE "%' . $this->escape_string($params->strings) . '%")');
				$table_strings->addJoinWhere(new QueryWhere('rule_id', 'rule.id', '=', 'field'));
				$table_strings->setJoinType('LEFT');
				$queryobj->addJoinTable($table_strings);
			}
			if ( isset($params->condition) && $params->condition != -1 ) {
				$table_rule->addWhere(new QueryWhere('cond', '%' . $this->escape_string($params->condition) . '%', 'LIKE', 'str'));
			}
			$table_rule->addWhere(new QueryWhere('status', self::status_recyclebin, '<>', 'text'));
		}
		
		// Common filters
		if ( isset($params->limit) && $params->limit != -1 ) {
			$queryobj->setLimits(0, $this->escape_string($this->$params->limit));
		}
		
		$queryobj->addTable($table_rule);
		$results = $this->Execute($queryobj);
		foreach($results as &$result)
		{
			$result["tags"] = array_filter(explode(',', $result["tags"]));
		}
		return $results;
	}
	
	//===================================================
	
	public function GetRule($rule_id) 
	{
		$stmt = $this->mysqli->prepare("SELECT r.id, r.file_id, r.name, r.cond, r.is_private, r.is_global, r.tags, r.modified, r.created 
				FROM rule r 
				WHERE r.id = ?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();
		$stmt->bind_result($id, $file_id, $name, $condition, $is_private, $is_global, $tags, $modified, $created);
		$results = array();
		if ($stmt->fetch()) {
			$results = array('id' => $id, 'file_id' => $file_id, 'name' => $name, 'cond' => $condition, 'is_private' => $is_private, 'is_global' => $is_global, 'tags' => $tags, 'last_modified' => $modified, 'created' => $created);
			$results["tags"] = array_filter(explode(",", $results["tags"]));
		}
		$stmt->close();		
		return $results;
	}
	
	public function RuleExists($rule_name)
	{
		$stmt = $this->mysqli->prepare("SELECT count(*) as count FROM rule WHERE name = ?");
		$stmt->bind_param("s", $rule_name);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();		
		return ((int) $count) > 0;
	}
	
	public function DeleteRule($rule_id) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM rule WHERE id=?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();		
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function CreateRule($rule_content) 
	{
		$tags = implode(",", $rule_content->tags);
		$stmt = $this->mysqli->prepare("INSERT INTO rule (file_id, name, cond, is_private, is_global, tags, created, modified) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
		$stmt->bind_param("issiis", $rule_content->file_id, $rule_content->name, $rule_content->condition, $rule_content->is_private, $rule_content->is_global, $tags);
		$stmt->execute();
		$id = $this->mysqli->insert_id;
		$stmt->close();		
		return $id;
	}
	
	public function CopyRule($rule_id) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO rule (file_id, name, cond, is_private, is_global, tags, created, modified) 
				SELECT file_id, name, cond, is_private, is_global, tags, NOW(), NOW() FROM rule WHERE id=?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();
		$id = $this->mysqli->insert_id;
		$stmt->close();		
		return $id;
	}
	
	public function UpdateRule($rule_id, $rule_content) 
	{
		$tags = implode(",", $rule_content->tags);
		$stmt = $this->mysqli->prepare("UPDATE rule SET file_id=?, name=?, cond=?, is_private=?, is_global=?, tags=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("issiisi", $rule_content->file_id, $rule_content->name, $rule_content->condition, $rule_content->is_private, $rule_content->is_global, $tags, $rule_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function RenameRule($rule_id, $rule_name) 
	{
		$stmt = $this->mysqli->prepare("UPDATE rule SET name=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("si", $rule_name, $rule_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function MoveRuleToRecycleBin($rule_id) 
	{
		$status = self::status_recyclebin;
		$stmt = $this->mysqli->prepare("UPDATE rule SET status=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("si", $status, $rule_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function RestoreRule($rule_id) 
	{
		$status = self::status_draft;
		$stmt = $this->mysqli->prepare("UPDATE rule SET status=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("si", $status, $rule_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function MoveRule($rule_id, $file_id) 
	{
		$stmt = $this->mysqli->prepare("UPDATE rule SET file_id=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("ii", $file_id, $rule_id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function DeleteRuleMetas($rule_id) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM rule_metas WHERE rule_id=?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	public function CopyRuleMetas($rule_id, $rule_copy_id) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO rule_metas (rule_id, name, value) SELECT ?, name, value FROM rule_metas WHERE rule_id=?");
		$stmt->bind_param("ii", $rule_copy_id, $rule_id);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	public function GetRuleMetaValue($rule_id, $meta_name) 
	{
		$results = $this->GetRuleMeta($rule_id, $meta_name);	
		if (isset($results['value'])) {
			return $results['value'];
		}
		return "";
	}
	
	public function GetRuleMeta($rule_id, $meta_name) 
	{
		$stmt = $this->mysqli->prepare("SELECT m.rule_id, m.name, m.value, m.is_custom FROM rule_metas m WHERE m.rule_id = ? AND m.name = ?");
		$stmt->bind_param("is", $rule_id, $meta_name);
		$stmt->execute();
		$stmt->bind_result($id, $name, $value, $is_custom);
		$results = array();
		if ($stmt->fetch()) {
			$results = array('id' => $id, 'name' => $name, 'value' => $value, 'is_custom' => $is_custom);
		}
		$stmt->close();		
		return $results;
	}
	
	public function CreateRuleMeta($rule_id, $meta_name, $meta_value) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO rule_metas (rule_id, name, value) VALUES (?, ?, ?)");
		$stmt->bind_param("iss", $rule_id, $meta_name, $meta_value);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	public function UpdateRuleMeta($rule_id, $meta_name, $meta_value) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO rule_metas (rule_id, name, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value=?");
		$stmt->bind_param("isss", $rule_id, $meta_name, $meta_value, $meta_value);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	//=========================================================
	
	public function GetMetas($rule_id) 
	{
		$stmt = $this->mysqli->prepare("SELECT rule_id, name, value, type FROM meta WHERE rule_id=?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();
		$stmt->bind_result($id, $name, $value, $type);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array('id' => $id, 'name' => $name, 'value' => $value, 'type' => $type);
		}
		$stmt->close();		
		return $results;
	}
	
	public function CreateOrUpdateMeta($rule_id, $meta_name, $meta_value, $meta_type) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO meta (rule_id, name, type, value) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE type=?, value=?");
		$stmt->bind_param("isssss", $rule_id, $meta_name, $meta_type, $meta_value, $meta_type, $meta_value);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	public function UpdateMeta($rule_id, $meta_name, $meta_value, $meta_type) 
	{
		$stmt = $this->mysqli->prepare("UPDATE meta SET type=?, value=? WHERE rule_id=? AND name=?");
		$stmt->bind_param("ssis", $meta_type, $meta_value, $rule_id, $meta_name);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function DeleteMeta($rule_id, $meta_name) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM meta WHERE rule_id=? AND name=?");
		$stmt->bind_param("is", $rule_id, $meta_name);
		$stmt->execute();
		$stmt->close();
		return true;
	}
	
	public function DeleteMetas($rule_id) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM meta WHERE rule_id=?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();
		$stmt->close();
		return true;
	}
	
	//=========================================================
	
	public function GetStrings($rule_id) 
	{
		$stmt = $this->mysqli->prepare("SELECT rule_id, name, value FROM string WHERE rule_id=?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();
		$stmt->bind_result($id, $name, $value);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array('id' => $id, 'name' => $name, 'value' => $value);
		}
		$stmt->close();		
		return $results;
	}
	
	public function CreateOrUpdateString($rule_id, $string_name, $string_value) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO string (rule_id, name, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value=?");
		$stmt->bind_param("isss", $rule_id, $string_name, $string_value, $string_value);
		$stmt->execute();
		$stmt->close();		
		return true;
	}
	
	public function UpdateString($rule_id, $string_name, $string_value) 
	{
		$stmt = $this->mysqli->prepare("UPDATE string SET value=? WHERE rule_id=? AND name=?");
		$stmt->bind_param("sis", $string_value, $rule_id, $string_name);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function DeleteString($rule_id, $string_name) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM string WHERE rule_id=? AND name=?");
		$stmt->bind_param("is", $rule_id, $string_name);
		$stmt->execute();
		$stmt->close();
		return true;
	}
	
	public function DeleteStrings($rule_id) 
	{
		$stmt = $this->mysqli->prepare("DELETE FROM string WHERE rule_id=?");
		$stmt->bind_param("i", $rule_id);
		$stmt->execute();
		$stmt->close();
		return true;
	}
	
	//=========================================================
	
	public function AddToHistory($item)
	{	
		$stmt = $this->mysqli->prepare("INSERT INTO history (user,date,action,item_id,item_type,item_name,item_value,item_old_value) VALUES (?,NOW(),?,?,?,?,?,?)");
		$stmt->bind_param("isissss", $item->user, $item->action, $item->item_id, $item->item_type, $item->item_name, $item->item_value, $item->item_oldvalue);
		$stmt->execute();
		$stmt->close();
	}
	
	public function ClearHistory()
	{	
		$stmt = $this->mysqli->prepare("DELETE FROM history");
		$stmt->execute();
		$stmt->close();
		return True;
	}
	
	public function GetHistory($limit = -1) 
	{
		$query = "SELECT id,user,date,action,item_id,item_type,item_name,item_value,item_old_value FROM history";	
		if ( $limit != -1 ) {
			$query = $query . " LIMIT " . $this->escape_string($limit);
		}
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->execute();
		$stmt->bind_result($id,$user,$date,$action,$item_id,$item_type,$item_name,$item_value,$item_old_value);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array(
				'id' => $id, 'user' => $user, 'date' => $date, 'action' => $action, 'item_id' => $item_id, 'item_type' => $item_type, 'item_name' => $item_name, 
				'item_value' => $item_value, 'item_old_value' => $item_old_value					
			);
		}
		$stmt->close();		
		return $results;
	}
	
	//=========================================================
	
	public function GetTags() 
	{
		$stmt = $this->mysqli->prepare("SELECT tags FROM rule WHERE tags <> ''");
		$stmt->execute();
		$stmt->bind_result($tags);
		$results = array();
		while ($stmt->fetch()) {
			$tags_exploded = array_filter(explode(",", $tags));	
			$results = array_merge($results, $tags_exploded);
		}
		$results = array_count_values($results);
		$stmt->close();	
		return $results;
	}
	
	public function GetSubmissionsPerUser() 
	{
		$stmt = $this->mysqli->prepare("SELECT value as uploader, COUNT(*) FROM `rule_metas` WHERE name = '__author' and value <> '' GROUP BY value");
		$stmt->execute();
		$stmt->bind_result($uploader, $count);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array('uploader' => $uploader, 'count' => $count);
		}
		$stmt->close();	
		return $results;
	}
	
	public function GetFilesCount() 
	{
		$stmt = $this->mysqli->prepare("SELECT count(*) as count FROM virtual_file");
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();		
		return (int) $count;
	}
	
	public function GetTotalRulesCount() 
	{
		$stmt = $this->mysqli->prepare("SELECT count(*) as count FROM rule");
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();		
		return (int) $count;
	}
	
	public function GetSubmissions($days_count = -1) 
	{
		$stmt = $this->mysqli->prepare("SELECT DATE(created) as date, count(*) as count FROM rule" 
				. ($days_count == -1 ? "" : " WHERE DATE(created) > DATE_SUB(NOW(), INTERVAL " . strval($days_count) . " DAY) ") . " GROUP BY DATE(created) ORDER BY date ASC");
		$stmt->execute();
		$stmt->bind_result($date, $count);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array('date' => $date, 'count' => $count);
		}
		$stmt->close();	
		return $results;
	}
	
	public function SearchThreat($request)
	{
		$meta_name = '__threat';
		$meta_value = $request . '%';
		$stmt = $this->mysqli->prepare("SELECT value FROM rule_metas WHERE name = ? AND value LIKE ? GROUP BY value");
		$stmt->bind_param("ss", $meta_name, $meta_value);
		$stmt->execute();
		$stmt->bind_result($value);
		$results = array();
		if ($stmt->fetch()) {
			$results[] = $value;
		}
		$stmt->close();		
		return $results;
	}
	
	public function SearchRuleName($request)
	{
		$rule_name = $request . '%';
		$stmt = $this->mysqli->prepare("SELECT id,name FROM rule WHERE name LIKE ? AND status <> \"" . self::status_recyclebin . "\"");
		$stmt->bind_param("s", $rule_name);
		$stmt->execute();
		$stmt->bind_result($id, $name);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array('id' => $id, 'name' => $name);
		}
		$stmt->close();	
		return $results;
	}
	
	//===============================================
	
	public function AddTestSet($name, $rule_id, $author) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO testset (rule_id, author, name, created, modified) VALUES (?, ?, ?, NOW(), NOW())");
		$stmt->bind_param("iss", $rule_id, $author, $name);
		$stmt->execute();
		$id = $this->mysqli->insert_id;
		$stmt->close();		
		return $id;
	}
	
	public function GetTestSets($rule_id = -1, $user = -1) 
	{		
		$queryobj = new QueryBuilder();
		$table_testset = new QueryTable('testset');
		$table_testset->setSelect(array(
				'id' => 'id',
				'rule_id' => 'rule_id',
				'author' => 'author_id',
				'name' => 'name',				
				'modified' => 'last_modified',
				'created' => 'created',
				'status' => 'status'
		));
		$table_testset->addGroupBy('id');
		$table_testset->addOrderBy(new QueryOrderBy('modified', 'DESC', True));				
		
		if ( $user != -1 ) {
			$table_testset->addWhere(new QueryWhere('author', $this->escape_string($user), '=', 'int'));
		}
		if ( $rule_id != -1 ) {
			$table_testset->addWhere(new QueryWhere('rule_id', $this->escape_string($rule_id), '=', 'int'));
		}
		
		$table_rules = new QueryTable('rule');
		$table_rules->setSelect(array('name' => 'rule_name'));	
		$table_rules->addJoinWhere(new QueryWhere('id', 'testset.rule_id', '=', 'field'));
		$queryobj->addJoinTable($table_rules);		
		
		$queryobj->addTable($table_testset);	
		$results = $this->Execute($queryobj);	
		return $results;
	}
	
	public function GetTestSet($id) 
	{		
		$stmt = $this->mysqli->prepare("SELECT t.rule_id, t.author, r.name as rule_name, t.name, t.created, t.modified, t.status 
				FROM testset t
				LEFT JOIN rule r on r.id = t.rule_id 
				WHERE t.id = ?"
		);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($rule_id, $author, $rule_name, $name, $created, $modified, $status);
		$stmt->fetch();
		$results = array('id' => $id, 'rule_id' => $rule_id, 'author' => $author, 'rule_name' => $rule_name, 'name' => $name, 'created' => $created, 'last_modified' => $modified, 'status' => $status);
		$stmt->close();		
		return $results;
	}
	
	public function UpdateTestSet($id, $name, $rule_id) 
	{
		$stmt = $this->mysqli->prepare("UPDATE testset SET rule_id=?, name=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("isi", $rule_id, $name, $id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function SetTestSetStatus($id, $status) 
	{
		$stmt = $this->mysqli->prepare("UPDATE testset SET status=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("si", $status, $id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function DeleteTestSet($id) 
	{		
		$stmt = $this->mysqli->prepare("DELETE FROM testset WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();		
		return $success;
	}
	
	public function AddTest($testset_id, $type, $content) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO test (set_id, created, modified, type, content, results) VALUES (?, NOW(), NOW(), ?, ?, '')");
		$stmt->bind_param("iss", $testset_id, $type, $content);
		$stmt->execute();
		$id = $this->mysqli->insert_id;
		$stmt->close();		
		return $id;
	}
	
	public function GetTests($testset_id) 
	{		
		$stmt = $this->mysqli->prepare("SELECT t.id, t.set_id, s.name as set_name, t.created, t.modified, t.status, t.type, t.content, t.results 
				FROM test t
				LEFT JOIN testset s on s.id = t.set_id
				WHERE t.set_id = ?"
		);
		$stmt->bind_param("i", $testset_id);
		$stmt->execute();
		$stmt->bind_result($id, $set_id, $set_name, $created, $modified, $status, $type, $content, $test_results);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array('id' => $id, 'set_id' => $set_id, 'set_name' => $set_name, 'created' => $created, 'last_modified' => $modified, 'status' => $status, 'type' => $type, 'content' => $content, 'results' => json_decode($test_results));
		}
		$stmt->close();		
		return $results;
	}
	
	public function GetTestsToUpdate() 
	{		
		$status = self::status_idle;
		$stmt = $this->mysqli->prepare("SELECT t.id, t.set_id, s.name as set_name, t.created, t.modified, t.status, t.type, t.content, t.results 
				FROM test t
				LEFT JOIN testset s on s.id = t.set_id
				WHERE t.status = ?"
		);
		$stmt->bind_param("s", $status);
		$stmt->execute();
		$stmt->bind_result($id, $set_id, $set_name, $created, $modified, $status, $type, $content, $test_results);
		$results = array();
		while ($stmt->fetch()) {
			$results[] = array('id' => $id, 'set_id' => $set_id, 'set_name' => $set_name, 'created' => $created, 'last_modified' => $modified, 'status' => $status, 'type' => $type, 'content' => $content, 'results' => json_decode($test_results));
		}
		$stmt->close();		
		return $results;
	}
	
	public function GetTest($id) 
	{		
		$stmt = $this->mysqli->prepare("SELECT t.id, t.set_id, s.name as set_name, t.created, t.modified, t.status, t.type, t.content, t.results 
				FROM test t
				LEFT JOIN testset s on s.id = t.set_id
				WHERE t.id = ?"
		);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->bind_result($id, $set_id, $set_name, $created, $modified, $status, $type, $content, $test_results);
		$stmt->fetch();
		$results = array('id' => $id, 'set_id' => $set_id, 'set_name' => $set_name, 'created' => $created, 'last_modified' => $modified, 'status' => $status, 'type' => $type, 'content' => $content, 'results' => json_decode($test_results));
		$stmt->close();		
		return $results;
	}
	
	public function UpdateTest($id, $type, $content) 
	{
		$stmt = $this->mysqli->prepare("UPDATE test SET type=?, content=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("ssi", $type, $content, $id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function SetTestResults($id, $results) 
	{
		$stmt = $this->mysqli->prepare("UPDATE test SET results=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("si", $results, $id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function SetTestStatus($id, $status) 
	{
		$stmt = $this->mysqli->prepare("UPDATE test SET status=?, modified=NOW() WHERE id=?");
		$stmt->bind_param("si", $status, $id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();
		return $success;
	}
	
	public function DeleteTest($id) 
	{		
		$stmt = $this->mysqli->prepare("DELETE FROM test WHERE id = ?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$success = $this->mysqli->affected_rows > 0;
		$stmt->close();		
		return $success;
	}
	
	public function CopyTest($id) 
	{
		$stmt = $this->mysqli->prepare("INSERT INTO test (set_id, created, modified, status, type, content, results) SELECT set_id, NOW(), NOW(), status, type, content, '' FROM test WHERE id=?");
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$id = $this->mysqli->insert_id;
		$stmt->close();		
		return $id;
	}
	
	//===============================================
	
	public function Create()
	{
		$success = true;
		
		$meta_sql = "
		CREATE TABLE `meta` (
		  `id` int(11) NOT NULL,
		  `rule_id` int(11) NOT NULL,
		  `name` text NOT NULL,
		  `type` text NOT NULL,
		  `value` text NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($meta_sql);
		if($stmt->execute())
		{
			echo "<p>meta table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing meta table.</p>";
			$success = false;
		}
		
		$meta_sql = "
		ALTER TABLE `meta`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `unique_meta` (`rule_id`,`name`(50)),
		  ADD KEY `rule_id` (`rule_id`);
		";	
		
		$stmt = $this->mysqli->prepare($meta_sql);
		if($stmt->execute())
		{
			echo "<p>meta table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing meta table index.</p>";
			$success = false;
		}
		
		$meta_sql = "
		ALTER TABLE `meta`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($meta_sql);
		if($stmt->execute())
		{
			echo "<p>meta table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing meta table increment.</p>";
			$success = false;
		}
		
		//=========================================
		
		$rule_sql = "
		CREATE TABLE `rule` (
		  `id` int(11) NOT NULL,
		  `file_id` int(11) NOT NULL,
		  `name` text NOT NULL,
		  `cond` text NOT NULL,
		  `is_private` int(11) NOT NULL DEFAULT '0',
		  `is_global` int(11) NOT NULL DEFAULT '0',
		  `tags` text NOT NULL,
		  `created` datetime NOT NULL,
		  `modified` datetime NOT NULL,
  		  `status` varchar(10) NOT NULL DEFAULT 'draft'
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($rule_sql);
		if($stmt->execute())
		{
			echo "<p>rule table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing rule table.</p>";
			$success = false;
		}
		
		$rule_sql = "
		ALTER TABLE `rule`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `file_id` (`file_id`);
		";	
		
		$stmt = $this->mysqli->prepare($rule_sql);
		if($stmt->execute())
		{
			echo "<p>rule table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing rule table index.</p>";
			$success = false;
		}
		
		$rule_sql = "
		ALTER TABLE `rule`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($rule_sql);
		if($stmt->execute())
		{
			echo "<p>rule table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing rule table increment.</p>";
			$success = false;
		}
		
		//=========================================
		
		$rule_metas_sql = "
		CREATE TABLE `rule_metas` (
		  `id` int(11) NOT NULL,
		  `rule_id` int(11) NOT NULL,
		  `name` text NOT NULL,
		  `value` text NOT NULL,
		  `is_custom` int(11) NOT NULL DEFAULT '0'
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($rule_metas_sql);
		if($stmt->execute())
		{
			echo "<p>rule metas table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing rule metas table.</p>";
			$success = false;
		}
		
		$rule_metas_sql = "
		ALTER TABLE `rule_metas`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `rule_id_2` (`rule_id`,`name`(30)),
		  ADD KEY `rule_id` (`rule_id`) USING BTREE;
		";	
		
		$stmt = $this->mysqli->prepare($rule_metas_sql);
		if($stmt->execute())
		{
			echo "<p>rule metas table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing rule metas table index.</p>";
			$success = false;
		}
		
		$rule_metas_sql = "
		ALTER TABLE `rule_metas`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($rule_metas_sql);
		if($stmt->execute())
		{
			echo "<p>rule metas table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing rule metas table increment.</p>";
			$success = false;
		}
		
		//=========================================
		
		$string_sql = "
		CREATE TABLE `string` (
		  `id` int(11) NOT NULL,
		  `rule_id` int(11) NOT NULL,
		  `name` text NOT NULL,
		  `value` text NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($string_sql);
		if($stmt->execute())
		{
			echo "<p>string table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing string table.</p>";
			$success = false;
		}
		
		$string_sql = "
		ALTER TABLE `string`
		  ADD PRIMARY KEY (`id`),
		  ADD UNIQUE KEY `unique_string` (`rule_id`,`name`(50)),
		  ADD KEY `rule_id` (`rule_id`);
		";	
		
		$stmt = $this->mysqli->prepare($string_sql);
		if($stmt->execute())
		{
			echo "<p>string table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing string table index.</p>";
			$success = false;
		}
		
		$string_sql = "
		ALTER TABLE `string`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($string_sql);
		if($stmt->execute())
		{
			echo "<p>string table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing string table increment.</p>";
			$success = false;
		}
		
		//=========================================
		
		$file_sql = "
		CREATE TABLE `virtual_file` (
		  `id` int(11) NOT NULL,
		  `name` text NOT NULL,
		  `imports` text NOT NULL,
		  `created` datetime NOT NULL,
		  `modified` datetime NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($file_sql);
		if($stmt->execute())
		{
			echo "<p>file table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing file table.</p>";
			$success = false;
		}
		
		$file_sql = "
		ALTER TABLE `virtual_file`
  		ADD PRIMARY KEY (`id`);
		";	
		
		$stmt = $this->mysqli->prepare($file_sql);
		if($stmt->execute())
		{
			echo "<p>file table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing file table index.</p>";
			$success = false;
		}
		
		$file_sql = "
		ALTER TABLE `virtual_file`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($file_sql);
		if($stmt->execute())
		{
			echo "<p>file table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing file table increment.</p>";
			$success = false;
		}
		
		//=========================================
		
		$file_metas_sql = "
		CREATE TABLE `virtual_file_metas` (
		  `id` int(11) NOT NULL,
		  `file_id` int(11) NOT NULL,
		  `name` text NOT NULL,
		  `value` text NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($file_metas_sql);
		if($stmt->execute())
		{
			echo "<p>file metas table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing file metas table.</p>";
			$success = false;
		}
		
		$file_metas_sql = "
		ALTER TABLE `virtual_file_metas`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `file_id` (`file_id`);
		";	
		
		$stmt = $this->mysqli->prepare($file_metas_sql);
		if($stmt->execute())
		{
			echo "<p>file metas table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing file metas table index.</p>";
			$success = false;
		}
		
		$file_metas_sql = "
		ALTER TABLE `virtual_file_metas`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($file_metas_sql);
		if($stmt->execute())
		{
			echo "<p>file metas table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing file metas table increment.</p>";
			$success = false;
		}
		
		//=========================================
		
		$history_sql = "
		CREATE TABLE `history` (
		  `id` int(11) NOT NULL,
		  `user` int(11) NOT NULL,
		  `date` datetime NOT NULL,
		  `action` text NOT NULL,
		  `item_id` int(11) NOT NULL,
		  `item_type` text NOT NULL,
		  `item_name` text NOT NULL,
		  `item_value` longtext NOT NULL,
		  `item_old_value` longtext NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($history_sql);
		if($stmt->execute())
		{
			echo "<p>history table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing history table.</p>";
			$success = false;
		}
		
		$history_sql = "
		ALTER TABLE `history`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `date` (`date`);
		";	
		
		$stmt = $this->mysqli->prepare($history_sql);
		if($stmt->execute())
		{
			echo "<p>history table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing history table index.</p>";
			$success = false;
		}
		
		$history_sql = "
		ALTER TABLE `history`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($history_sql);
		if($stmt->execute())
		{
			echo "<p>history table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing history table increment.</p>";
			$success = false;
		}
		
		//=========================================
		
		$test_sql = "
		CREATE TABLE `test` (
		  `id` int(11) NOT NULL,
		  `set_id` int(11) NOT NULL,
		  `created` datetime NOT NULL,
		  `modified` datetime NOT NULL,
		  `status` varchar(10) NOT NULL DEFAULT 'idle',
		  `type` varchar(15) NOT NULL,
		  `content` longtext NOT NULL,
		  `results` longtext NOT NULL
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($test_sql);
		if($stmt->execute())
		{
			echo "<p>test table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing test table.</p>";
			$success = false;
		}
		
		$test_sql = "
		ALTER TABLE `test`
		  ADD PRIMARY KEY (`id`),
		  ADD KEY `set_id` (`set_id`);
		";	
		
		$stmt = $this->mysqli->prepare($test_sql);
		if($stmt->execute())
		{
			echo "<p>test table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing test table index.</p>";
			$success = false;
		}
		
		$test_sql = "
		ALTER TABLE `test`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($test_sql);
		if($stmt->execute())
		{
			echo "<p>test table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing test table increment.</p>";
			$success = false;
		}
		
		//=========================================		
		
		$testset_sql = "
		CREATE TABLE `testset` (
		  `id` int(11) NOT NULL,
		  `rule_id` int(11) NOT NULL,
		  `author` int(11) NOT NULL,
		  `name` text NOT NULL,
		  `created` datetime NOT NULL,
		  `modified` datetime NOT NULL,
		  `status` varchar(10) NOT NULL DEFAULT 'idle'
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;
		";
		
		$stmt = $this->mysqli->prepare($testset_sql);
		if($stmt->execute())
		{
			echo "<p>testset table created.....</p>";
		}
		else
		{
			echo "<p>Error constructing testset table.</p>";
			$success = false;
		}
		
		$testset_sql = "
		ALTER TABLE `testset`
		  ADD PRIMARY KEY (`id`);
		";	
		
		$stmt = $this->mysqli->prepare($testset_sql);
		if($stmt->execute())
		{
			echo "<p>testset table index created.....</p>";
		}
		else
		{
			echo "<p>Error constructing testset table index.</p>";
			$success = false;
		}
		
		$testset_sql = "
		ALTER TABLE `testset`
  		MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
		";	
		
		$stmt = $this->mysqli->prepare($testset_sql);
		if($stmt->execute())
		{
			echo "<p>testset table increment created.....</p>";
		}
		else
		{
			echo "<p>Error constructing testset table increment.</p>";
			$success = false;
		}
		
		//=========================================		
		
		return $success;
	}
}
