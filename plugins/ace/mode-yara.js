
//================================================================
// Mode
ace.define('ace/mode/yara', function(require, exports, module) {

var oop = require("ace/lib/oop");
var TextMode = require("ace/mode/text").Mode;
var YaraHighlightRules = require("ace/mode/yara_highlight_rules").YaraHighlightRules;

var Mode = function() {
    this.HighlightRules = YaraHighlightRules;
};
oop.inherits(Mode, TextMode);

(function() {
    // Extra logic goes here. (see below)
}).call(Mode.prototype);

exports.Mode = Mode;
});

//==================================================================
// Rules
ace.define('ace/mode/yara_highlight_rules', function(require, exports, module) {

var oop = require("ace/lib/oop");
var TextHighlightRules = require("ace/mode/text_highlight_rules").TextHighlightRules;

var identifierRe = "[a-zA-Z\\$_\u00a1-\uffff][a-zA-Z\\d\\$_\u00a1-\uffff]*";

var YaraHighlightRules = function() {

	// Keywords
	var keywordMapper = this.createKeywordMapper({
        "keyword":
            "all|and|any|ascii|at|condition|contains|" +
            "entrypoint|false|filesize|fullword|for|global|in" +
            "import|include|int8|int16|int32|int8be|int16be|int32be" +
            "matches|meta|nocase|not|or|of|private|rule|strings|them|true" +
            "uint8|uint16|uint32|uint8be|uint16be|uint32be|wide"
    }, "identifier");
	
	// Comments
	this.lineCommentStart = "//";
    this.blockComment = {start: "/*", end: "*/"};

	// Rules
	this.$rules = {
		start: [ 
			{
                token: "comment.line",
                regex: "\\/\\/.*$"
            },
			{
                token: "comment.block",
                // multi line comment
                regex: "\\/\\*",
                next: "comment"
            },
			{
                token: ["keyword", "entity.name.function", "text", "entity.name.tag"], 
                regex: /(rule )(\w*)(\s*\:)([\s\w]*)$/
            },
			{
                token: "string",
                // single line
                regex: '["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]'
            }, 
			{
                token: "string",
                // single line
                regex: "['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"
            }, 
			{
                token: "constant.numeric",
                // hex
                regex: /0(?:[xX][0-9a-fA-F][0-9a-fA-F_]*|[bB][01][01_]*)[LlSsDdFfYy]?\b/
            }, 
			{
                token: "constant.numeric",
                // float
                regex: /[+-]?\d[\d_]*(?:(?:\.[\d_]*)?(?:[eE][+-]?[\d_]+)?)?[LlSsDdFfYy]?\b/
            }, 
			{
                token: "constant.language.boolean",
                regex: "(?:true|false)\\b"
            },
			{
				token : keywordMapper,
				regex : identifierRe
			},
			{
                token: "keyword.operator",
                regex: "!|\\$|%|&|\\*|\\-\\-|\\-|\\+\\+|\\+|~|===|==|=|!=|!==|<=|>=|<<=|>>=|>>>=|<>|<|>|!|&&|\\|\\||\\?\\:|\\*=|%=|\\+=|\\-=|&=|\\^=|\\b(?:in|instanceof|new|delete|typeof|void)"
            }
		],
		"comment": [
			{
                token: "comment.block",
                // closing comment
                regex: ".*?\\*\\/",
                next: "start"
            }, 
			{
                token: "comment.block",
                // comment spanning whole line
                regex: ".+"
            }
		]
	};
	
}

oop.inherits(YaraHighlightRules, TextHighlightRules);

exports.YaraHighlightRules = YaraHighlightRules;
});