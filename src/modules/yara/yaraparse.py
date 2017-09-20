#!/usr/bin/env python
import os, sys, json, string, argparse, re
import yara #pip install yara-python
from __builtin__ import file

def parse_args():
    global filepath
    global testitem
    global stream_id
    
    argsParser = argparse.ArgumentParser(usage='Parse Yara information')
    argsParser.add_argument('-f', '--file', dest='filepath', default='', help='The yara file that will be used', required=True)
    argsParser.add_argument('-t', '--testitem', dest='testitem', default='', help='The yara test item that will be used', required=False)
    args = argsParser.parse_args()
    
    filepath = os.path.normpath(args.filepath)
    testitem = ''
    if args.testitem:
        testitem = os.path.normpath(args.testitem)

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
            
        encoded = json.dumps(data)
        print encoded
    except Exception as ex:
        data = {}
        data['valid'] = False            
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
else:
    code = TestCompile(filepath)
    exit(code)

