<?php

require_once(__DIR__.'/module.php');
require_once(__DIR__.'/../utils.php');

class YaraData extends IModule
{	
	public function OnYaraCheckSyntax(&$data) 
	{
		$tmp_file = '';
		if (!$this->DumpRuleToTmp($data['rule_export'], $tmp_file)) {
			return;
		}		
		$data['rule_check'] = json_decode($this->CheckSyntax($tmp_file));		
		unlink($tmp_file);
	}
	
	public function OnYaraTest(&$data) 
	{
		$tmp_file = '';
		if (!$this->DumpRuleToTmp($data['rule_export'], $tmp_file)) {
			return;
		}	
		if (!isset($data['test_type'])) {
			return;
		}
		if (!isset($data['test_data'])) {
			return;
		}		
		
		$results = '';
		if ($data['test_type'] == 'string_ansi') {
			$results = $this->TestStringAnsi($tmp_file, $data['test_data']);
		}
		else if ($data['test_type'] == 'string_unicode') {
			$results = $this->TestStringUnicode($tmp_file, $data['test_data']);
		}
		
		$data['rule_test'] = json_decode($results);
		unlink($tmp_file);
	}	
	
	//=================================================
	
	private function TestStringAnsi($sigs_file, $content)
	{
		$tmp_file = '';
		if (!$this->DumpStringToTmp($content, $tmp_file)) {
			return NULL;
		}		
		$results = $this->RunTest($sigs_file, $tmp_file);		
		unlink($tmp_file);
		return $results;
	}
	
	private function TestStringUnicode($sigs_file, $content)
	{
		// Convert to unicode
		$content = mb_convert_encoding($content, "UTF-16LE");
		
		$tmp_file = '';
		if (!$this->DumpStringToTmp($content, $tmp_file)) {
			return NULL;
		}		
		$results = $this->RunTest($sigs_file, $tmp_file);		
		unlink($tmp_file);
		return $results;
	}
	
	//=================================================
	
	private function CheckSyntax($sigs_file)
	{		
		$command = 'python "'.__DIR__.'/yara/yaraparse.py" --file "'.$sigs_file.'"';
		ob_start();
		system($command, $retcode);
		$output = ob_get_contents();
		ob_end_clean();
		if ($retcode == 0 || $retcode == 1)
			return $output;
		return '';
	}
	
	private function RunTest($sigs_file, $test_file)
	{		
		$command = 'python "'.__DIR__.'/yara/yaraparse.py" --file "'.$sigs_file.'" --testitem "'.$test_file.'"';
		ob_start();
		system($command, $retcode);
		$output = ob_get_contents();
		ob_end_clean();
		if ($retcode == 0 || $retcode == 1)
			return $output;
		return '';
	}
	
	//=================================================
	
	private function DumpStringToTmp($content, &$tmp_file)
	{
		if (!isset($content)) {
			return false;
		}		
		$tmp_file = tempnam(sys_get_temp_dir(), 'yed_');
		if (!file_put_contents($tmp_file, $content)) {
			return false;
		}
		return true;
	}
	
	private function DumpRuleToTmp($rule_content, &$tmp_file)
	{
		if (!isset($rule_content)) {
			return false;
		}		
		$tmp_file = tempnam(sys_get_temp_dir(), 'yed_');
		if (!file_put_contents($tmp_file, $rule_content)) {
			return false;
		}
		return true;
	}
}