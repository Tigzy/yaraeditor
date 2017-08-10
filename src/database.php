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
				LEFT JOIN rule r on r.file_id = vf.id
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
		$stmt = $this->mysqli->prepare("SELECT count(*) as count FROM rule WHERE file_id=?");
		$stmt->bind_param($file_id);
		$stmt->execute();
		$stmt->bind_result($count);
		$stmt->fetch();
		$stmt->close();		
		return (int) $count;
	}
	
	public function GetRules($file_id) 
	{
		$stmt = $this->mysqli->prepare("SELECT r.id, r.name, r.cond, r.is_private, r.is_global, r.tags, r.modified, r.created, 
				(SELECT m.value FROM rule_metas m WHERE m.rule_id = r.id AND m.name = '__threat') as threat,
				(SELECT m.value FROM rule_metas m WHERE m.rule_id = r.id AND m.name = '__author') as author
				FROM rule r
				WHERE r.file_id = ?");
		$stmt->bind_param("i", $file_id);
		$stmt->execute();
		$stmt->bind_result($id, $name, $condition, $is_private, $is_global, $tags, $modified, $created, $threat, $author_id);
		$results = array();
		while ($stmt->fetch()) {
			$tags_exploded = array_filter(explode(',', $tags));	
			$results[] = array(
				'id' => $id, 'name' => $name, 'cond' => $condition, 'is_private' => $is_private, 'is_global' => $is_global, 
				'tags' => $tags_exploded, 'last_modified' => $modified, 'created' => $created, 'threat' => $threat, 'author_id' => $author_id
			);
		}
		$stmt->close();		
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
	
	public function MoveRule($rule_id, $file_id) 
	{
		$tags = implode(",", $rule_content->tags);		
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
		  `modified` datetime NOT NULL
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
		
		return $success;
	}
}
