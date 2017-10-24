#!/usr/bin/env python
import os, sys, json, string, argparse, re, binascii
import yara #pip install yara-python
from plyara.plyara import PlyaraParser #pip install ply
from __builtin__ import file

def parse_args():
    global filepath
    global testitem
    global parseitem
    
    argsParser = argparse.ArgumentParser(usage='Parse Yara information')
    argsParser.add_argument('-f', '--file', dest='filepath', default='', help='The yara file that will be used', required=True)
    argsParser.add_argument('-t', '--testitem', dest='testitem', default='', help='The item will be tested for syntax', required=False)
    argsParser.add_argument('-p', '--parseitem', action='store_true', default=False, help='The item will be parsed into a dict', required=False)
    args = argsParser.parse_args()
    
    filepath = os.path.normpath(args.filepath)
    testitem = ''
    parseitem = False
    if args.testitem:
        testitem = os.path.normpath(args.testitem)
    elif args.parseitem:
        parseitem = True

def convert_char(char):
    if char in string.ascii_letters or char in string.digits or char in string.punctuation or char in string.whitespace or char == '\r' or char == '\n':
        return char            
    else:
        return '.'
  
def convert_to_printable_null_terminated(s):
    str_list = []
    if (s is None):
        return ''.join(str_list)
    for c in s:
        if (c=='\0'):   #null byte is here to mark the end of the string
            str_list.append(c)
            break;
        else:
            str_list.append(convert_char(c))
    
    return ''.join(str_list)

def convert_to_printable_string(s):
    str_list = []
    if s is not None:
        for c in s:
            str_list.append(convert_char(c))
    
    return ''.join(str_list)

def convert_to_printable(obj):
    if isinstance(obj, str):
        return convert_to_printable_string(obj)
    
    if isinstance(obj, int):
        return str(obj)
    
    if isinstance(obj, float):
        return str(obj)
        
    if isinstance(obj, list):
        new_obj = []
        for _, value in enumerate(obj):
            new_obj.append(convert_to_printable(value))
        return new_obj
        
    if isinstance(obj, dict):
        new_obj = {}
        for key, value in obj.iteritems():
            new_obj[key] = convert_to_printable(value)
        return new_obj
    
    return "<error: not parsed>"

def ParseError(err):    
    splitted = err.split(":")
    if len(splitted) > 2: #This may happen on Windows with X: path
        splitted_copy = []
        splitted_copy.append(':'.join(splitted[0:len(splitted) - 1]))
        splitted_copy = splitted_copy + splitted[len(splitted) - 1:]       
        splitted = splitted_copy
    
    if len(splitted) == 2:
        location    = splitted[0].strip()
        message     = splitted[1].strip()
        location_split = re.split(r'\((.*?)\)',location)
        location_split = filter(None, location_split);
        if len(location_split) == 2:
            file = location_split[0].strip()
            line = location_split[1].strip()
            
        return (file, line, message)
        
    return None, None, err

def TestCompile(path):
    if not(os.path.isfile(path)):
        print '{0} not a file!'.format(path)
        return 2

    try:
        data = {}
        data['valid']   = True  
        _               = yara.compile(filepath=path, error_on_warning=True)         
        encoded         = json.dumps(data)
        print encoded
    except Exception as ex:
        data = {}
        data['valid'] = False
        
        file, line, message = ParseError(str(ex))
        if file is not None:  
            data['error'] = {}
            data['error']['file'] = file
            data['error']['line'] = line
            data['error']['message'] = message
        else:
            data['error'] = str(ex)
            
        print json.dumps(data)
        return 1
        
    return 0

def stringify(input):
    if isinstance(input, dict):
        return {stringify(key): stringify(value)
                for key, value in input.iteritems()}
    elif isinstance(input, list):
        return [stringify(element) for element in input]
    elif isinstance(input, unicode):
        return input.decode('utf-8')
    elif isinstance(input, str):
        try:
            output = input.decode('utf-8')
            return output
        except:
            binstr = binascii.hexlify(input)
            return binstr.decode('utf-8').upper()
            
    elif isinstance(input, tuple):
        return tuple(stringify(value) for value in input)
    else:
        return input

def TestFile(path, testitem):
    if not(os.path.isfile(path)):
        print '{0} not a file!'.format(path)
        return 2

    try:
        data = {}
        data['valid']   = True  
        rules           = yara.compile(filepath=path, error_on_warning=False)       
        with open(testitem, 'rb') as f:
            matches = rules.match(data=f.read())  
            data["matches"] = []
            data["has_matches"] = len(matches) > 0
            for item in matches:
                match = {}
                match["rule"]       = item.rule
                match["strings"]    = item.strings
                match["tags"]       = item.tags
                match["metas"]      = item.meta
                data["matches"].append(match)
            
        data = stringify(data)
        encoded = json.dumps(data)
        print encoded
    except Exception as ex:
        data = {}
        data['valid'] = False            
        print json.dumps(data)
        return 1
        
    return 0

def FormatCondition(condition):
    nosep_before    = ['(', 'KB', 'MB']
    nosep_after     = []
    sep             = ' '
    
    joined_condition    = ''
    noseparator         = True # No separator on first item
    for term in condition: 
        if not term in nosep_before and not noseparator:
            joined_condition += sep # Add separator
        
        joined_condition += term    # Add term        
        noseparator      = True if term in nosep_after else False
    
    return joined_condition

def FormatParsing(results):
    data = []
    for rule in results:
        f_rule = {}
        
        # header
        f_rule["name"] = rule["rule_name"]    
        f_rule["comment"] = ""
        
        # tags
        f_rule["tags"] = []
        if "tags" in rule:
            f_rule["tags"] = rule["tags"]   
        
        # imports
        f_rule["imports"] = []
        if "imports" in rule:
            f_rule["imports"] = rule["imports"]
            
        # scope
        f_rule["is_private"]    = False
        f_rule["is_global"]     = False
        if "scopes" in rule:
            f_rule["is_private"]    = ('private' in rule["scopes"])
            f_rule["is_global"]     = ('global' in rule["scopes"])
            
        # metas
        f_rule["metas"] = []
        f_rule["author"] = ""
        f_rule["threat"] = ""
        if "metadata" in rule:
            for key, value in rule["metadata"].iteritems():
                if value == "\"\"":
                    value = ""
                if key == "threat":
                    f_rule["threat"] = value
                else:
                    real_key = key
                    if real_key in ["author", ""]:  # forbidden keys
                        real_key = "_" + real_key
                    
                    meta = {}
                    meta["name"]  = real_key
                    meta["value"] = value
                    f_rule["metas"].append(meta)
        
        # strings
        f_rule["strings"] = []
        if "strings" in rule:
            for string in rule["strings"]:
                if 'name' in string and 'value' in string:               
                    new_string = {}
                    new_string["name"]  = string['name']
                    new_string["value"] = string['value']
                    if 'modifiers' in string:
                        new_string["value"] = new_string["value"] + " " + " ".join([str(x) for x in string['modifiers']])
                    
                    f_rule["strings"].append(new_string)
        
        # condition
        f_rule["condition"] = FormatCondition(rule['condition_terms'])
        
        data.append(f_rule)
        
    return data

def ParseFile(path):
    if not(os.path.isfile(path)):
        print '{0} not a file!'.format(path)
        return 2

    try:
        data = {}
        data['valid']   = True    
        parser          = PlyaraParser()           
        rules           = parser.parseFromFile(path)   
        rules           = FormatParsing(rules)
        data            = stringify(rules)
        encoded         = json.dumps(data)
        #[{"condition_terms": ["any", "of", "them"], "rule_name": "RuleProgramAdw_DNSUnlocker", "metadata": {"modified": "\"04/11/2017 16:49:48\"", "threat": "\"Adw.DNSUnlocker\"", "created": "\"12/15/2016 00:00:00\""}, "strings": [{"modifiers": ["ascii"], "name": "$0", "value": "\"test.dll\""}], "tags": ["__ProgramRuleGenerated", "SignatureBlacklist"]}]
        print encoded
    except Exception as ex:
        data = {}
        data['valid'] = False
        data['error'] = str(ex)
        print json.dumps(data)
        return 1
        
    return 0

#--------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------
#--------------------------------------------------------------------------------------------------
cwd = os.path.dirname(os.path.realpath(__file__))

parse_args()

# validate path input
if (filepath == None):
    print('A path specification is required')
    exit(2)

if os.path.isdir(filepath):
    print 'please specify a file arg'
    exit(2)
elif testitem:
    code = TestFile(filepath, testitem)
    exit(code)
elif parseitem:
    code = ParseFile(filepath)
    exit(code)
else:
    code = TestCompile(filepath)
    exit(code)

