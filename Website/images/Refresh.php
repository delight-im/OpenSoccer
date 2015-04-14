<?php
header('Content-type: text/css');
header('Cache-control: max-age=604800');
ob_start('compress');
function compress($buffer) {
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer); // remove comments
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t"), '', $buffer); // remove white spaces
	$buffer = preg_replace('/\s\s+/', ' ', $buffer);
	if (stripos($_SERVER["HTTP_ACCEPT_ENCODING"],'x-gzip') !== false) {
		header('Content-encoding: x-gzip');
		$buffer = gzencode($buffer);
	}
	elseif (stripos($_SERVER["HTTP_ACCEPT_ENCODING"],'gzip') !== false) {
		header('Content-encoding: gzip');
		$buffer = gzencode($buffer);
	}
	elseif (stripos($_SERVER["HTTP_ACCEPT_ENCODING"],'deflate') !== false) {
		header('Content-encoding: deflate');
		$buffer = gzdeflate($buffer);
	}
	header('Content-length: '.strlen($buffer));
	return $buffer;
}
?>

/********************************************
AUTHOR: Erwin Aligam
WEBSITE: http://www.styleshout.com/
TEMPLATE NAME: Refresh
TEMPLATE CODE: S-0002
VERSION: 1.0
*******************************************/

/********************************************
   HTML ELEMENTS
********************************************/

/* Top Elements */
* { margin: 0; padding: 0; }

body {
	background-color: #ccc;
	font: 70%/1.5em Verdana, Tahoma, Arial, sans-serif;
	color: #333;
	text-align: center;
}

/* links */
a, a:visited {
	text-decoration: none;
	color: #4F82CB;
	background: inherit;
}
a:hover {
	color: #4EBF37;
	background: inherit;
}

/* headers */
h1, h2, h3 {
	font-family: Tahoma, Verdana, 'Trebuchet MS', Sans-serif;
	font-weight: Bold;
}
h1 {
	font-size: 120%;
}
h2 {
	font-size: 110%;
	text-transform: uppercase;
	color: #88ac0b;
}
h3 {
	font-size: 110%;
	color: #666666;
}

/* images */
img {
	border: 0px solid #fff;
}
img.float-right {
  margin: 5px 0px 10px 10px;
}
img.float-left {
  margin: 5px 10px 10px 0px;
}

h1, h2, h3, p {
	padding: 10px;
	margin: 0;
}
ul, ol {
	margin: 0px;
	padding: 10px 20px;
	color: #333;
}
ul span, ol span {
	color: #666666;
}
ul li {
	font-size: 100%;
}

code {
  margin: 5px 0;
  padding: 10px;
  text-align: left;
  display: block;
  overflow: auto;
  font: 500 1em/1.5em 'Lucida Console', 'courier new', monospace ;
  /* white-space: pre; */
  background: #FAFAFA;
  border: 1px solid #f2f2f2;
}
select {
	width: 200px;
}
select.long {
	width: 400px;
}
acronym {
  cursor: help;
  border-bottom: 1px solid #777;
}
blockquote {
	margin: 10px;
 	padding: 0 0 0 35px;
    border: 1px solid #999;
  	background: #eee url(/images/quote.png) no-repeat 5px 5px;
}

/* form elements */
form {
	margin:2px; padding: 0 5px;
}
label {
	display:block;
	font-weight:bold;
	margin:2px 0;
}
input {
	padding: 2px;
	border: 1px solid #999;
	font: normal normal normal 1em Verdana, sans-serif;
	color: #000;
}
input:hover {
	padding: 2px;
	border: 1px solid #ccc;
	font: normal normal normal 1em Verdana, sans-serif;
	color: #000;
}
textarea {
	width: 400px;
	padding: 2px;
	font: normal normal normal 1em Verdana, sans-serif;
	border: 1px solid #999;
	height: 100px;
	display: block;
	color: #000;
}
input:focus, textarea:focus {
	border-color: #999;
	}
input.button {
	margin: 0;
	font: normal normal bolder 12px Arial, Sans-serif;
	border: 1px solid #999;
	padding: 2px 3px;
	background: #fff;
	color: #000;
}

/* search */
form.search {
	position: absolute;
	top: 35px; right: 25px;
	background: transparent;
	border: none;
}
form.search input.textbox {
	margin: 0; padding: 1px 2px;
	width: 120px;
	background: #FFF;
	color: #333;
}
form.search input.button {
	background: #999 url(/images/headerbg.gif) repeat-x;
	color: #333;
	border: none;
	width: 70px; height: 21px;
}

/********************************************
   LAYOUT
********************************************/
#wrap {
	width: 820px;
	background-color: #fff;
	margin: 24px auto;
	padding: 12px;
	-webkit-border-radius: 8px;
	-moz-border-radius: 8px;
	border-radius: 8px;
	text-align: left;
}
#content-wrap {
	clear: both;
	width: 820px;
	padding: 0;
	margin: 0 auto;
}
#header {
	width: 820px;
	position: relative;
	background: -webkit-linear-gradient(to right, #467CD2, #2757A3);
	background: -moz-linear-gradient(to right, #467CD2, #2757A3);
	background: linear-gradient(to right, #467CD2, #2757A3);
	padding: 0;
	font-size: 14px;
	color: #FFF;
}
#header h1#logo-text {
	position: absolute;
	margin: 0; padding: 0;
	font: bolder 3.3em 'Trebuchet MS', Arial, Sans-serif;
	letter-spacing: -2px;
	color: #FFF;
	text-transform: none;

	/* change the values of top and left to adjust the position of the logo*/
	top: 28px; left: 50px;
}
#header h2#slogan {
	position: absolute;
	margin: 0; padding: 0;
	font: normal .8em 'Trebuchet MS', Arial, Sans-serif;
	text-transform: none;
	color: #FFF;

	/* change the values of top and left to adjust the position of the slogan*/
	top: 74px; left: 58px;
}

/* Menu */
#menu {
	clear: both;
	margin: 0;
	padding: 0 8px;
	background: -webkit-linear-gradient(to right, #82C13E, #63932F);
	background: -moz-linear-gradient(to right, #82C13E, #63932F);
	background: linear-gradient(to right, #82C13E, #63932F);
	font: bold 12px/26px Verdana, Arial, Tahoma, Sans-serif;
	text-shadow: 0 1px 1px rgba(0,0,0,0.5);
	height: 26px;
	z-index: 2;
}
#menu ul {
	float: right;
	list-style: none;
	margin:0; padding: 0;
}
#menu ul li {
	display: inline;
}
#menu ul li a {
	display: block;
	float: left;
	padding: 0 8px;
	color: #FFF;
	text-decoration: none;
}
#menu ul li a:hover {
	background-color: #ECECEC;
	color: #333;
}
#menu ul li#current {
	text-shadow: none;
}
#menu ul li#current a {
	background-color: #FFF;
	color: #333;
}

/* Main Column */
#main {
	float: right;
	width: 72%;
	padding: 0; margin: 0;
}
#main h1 {
	margin-top: 10px;
	font: Bold 125% Verdana, 'Trebuchet MS', Sans-serif;
	color: #88ac0b;
	padding: 5px 0 5px 25px;
	border-bottom: 1px solid #EFF0F1;
	background: #FFF url(/images/square-green.png) no-repeat 3px 50%;
}

#main_full {
	float: right;
	width: 100%;
	padding: 0; margin: 0;
}
#main_full h1 {
	margin-top: 10px;
	font: Bold 125% Verdana, 'Trebuchet MS', Sans-serif;
	color: #88ac0b;
	padding: 5px 0 5px 25px;
	border-bottom: 1px solid #EFF0F1;
	background: #FFF url(/images/square-green.png) no-repeat 3px 50%;
}

.post-footer {
	background-color: #FAFAFA;
	padding: 5px; margin: 20px 10px 0 10px;
	border: 1px solid #f2f2f2;
	font-size: 95%;
}
.post-footer .date {
	background: url(/images/clock.gif) no-repeat left center;
	padding-left: 20px; margin: 0 10px 0 5px;
}
.post-footer .comments {
	background: url(/images/comment.gif) no-repeat left center;
	padding-left: 20px; margin: 0 10px 0 5px;
}
.post-footer .readmore {
	background: url(/images/page.gif) no-repeat left center;
	padding-left: 20px; margin: 0 10px 0 5px;
}

/* Sidebar */
#sidebar {
	float: left;
	width: 26.5%;
	padding: 0; margin: 0;
}
#sidebar h1 {
	margin-top: 10px;
	padding: 5px 0 5px 10px;
	font: bold 1.1em Verdana, 'Trebuchet MS', Sans-serif;
	color: #555;
	background: #EEF0F1 url(/images/headerbg.gif) repeat-x left bottom;
	border: 1px solid #EFF0F1;
}
#sidebar .left-box {
	border: 1px solid #EFF0F1;
	margin: 0 0 5px 0;
}
#sidebar ul.sidemenu {
	list-style: none;
	text-align: left;
	margin: 3px 0 8px 0; padding: 0;
	text-decoration: none;
}
#sidebar ul.sidemenu li {
	border-bottom: 1px solid #EFF0F1;
	background: url(/images/go.gif) no-repeat 5px 5px;
	padding: 2px 0 2px 25px;
	margin: 0 2px;
}
#sidebar ul.sidemenu a {
	font-weight: bolder;
	text-decoration: none;
	background-image: none;
}

/* Footer */
#footer {
	color: #666666;
	background-color: #fff;
	clear: both;
	width: 820px;
	height: 55px;
	text-align: center;
	font-size: 92%;
}
#footer a { text-decoration: none; }

/* alignment classes */
.float-left  { float: left; }
.float-right { float: right; }
.align-left  { text-align: left; }
.align-right { text-align: right; }

/* display and additional classes */
.clear { clear: both; }
.gray { color: #ccc; }

table a, table a:link, table a:visited {
	border:none;
}
table {
	width: 100%;
	border-top: 1px solid #e5eff8;
	border-right: 1px solid #e5eff8;
	margin: 1em auto;
	border-collapse: collapse;
}
caption {
	color: #9ba9b4;
	font-size: .94em;
	letter-spacing: 0.1em;
	margin: 1em 0 0 0;
	padding: 0;
	caption-side: top;
	text-align: center;
}
tr.odd td {
	background: #f7fbff
}
tr.odd .column1	{
	background: #f4f9fe;
}
.column1 {
	background: #f9fcfe;
}
td {
	color: #678197;
	border-bottom: 1px solid #e5eff8;
	border-left: 1px solid #e5eff8;
	text-align: left;
	padding: 0.4em 1em;
}
td.link {
	color: #678197;
	border-bottom: 1px solid #e5eff8;
	border-left: 1px solid #e5eff8;
	text-align: left;
	padding: 0;
}
td.link a, td.link a:link, td.link a:visited {
	display: block;
	padding: 0.4em 1em;
	text-align: left;
	background-color: inherit;
	text-decoration: none;
}
td.link a:hover {
	background-color: #88ac0b;
	color: #fff;
	text-decoration: none;
}
th {
	font-weight: normal;
	color: #678197;
	text-align: left;
	border-bottom: 1px solid #e5eff8;
	border-left: 1px solid #e5eff8;
	padding: 0.3em 1em;
}
thead th {
	background: #f4f9fe;
	text-align: left;
	font-weight: bold;
	font-size: 1.2em;
	line-height: 2em;
	color: #66a3d3
}
tfoot th {
	text-align: left;
	background: #f4f9fe;
}
tfoot th strong {
	font-weight: bold;
	font-size: 1.2em;
	margin: 0.5em 0.5em 0.5em 0;
	color: #66a3d3;
}
tfoot th em {
	color: #f03b58;
	font-weight: bold;
	font-size: 1.1em;
	font-style: normal;
}

.pagebar {
    font: normal normal 11px Arial, Helvetica, sans-serif;
    padding-top: 30px;
    padding-bottom: 10px;
    margin: 0px;
    text-align: center;
}
.pagebar a, .pageList .this-page {
    padding: 2px 6px;
    border: solid 1px #ddd;
    background: #fff;
    text-decoration: none;
}
.pagebar a:visited {
    padding: 2px 6px;
    border: solid 1px #ddd;
    background: #fff;
    text-decoration: none;
}
.pagebar .break {
    padding: 2px 6px;
    border: none;
    background: #fff;
    text-decoration: none;
}
.pagebar .this-page {
    padding: 2px 6px;
    border: solid 1px #ddd;
    background: #f2f2f2;
    text-decoration: none;
    font-weight: bold;
}
.pagebar a:hover {
    color: #fff;
    background: #0063dc;
    border-color: #036;
    text-decoration: none;
}
#rfooter a {
	text-decoration: underline;
	color: #666;
	}

.infokasten {
	border: 10px solid #88ac0b;
	padding: 10px;
	width: 350px;
	margin-left: auto;
	margin-right: auto;
	margin-top: 10px;
	margin-bottom: 10px;
}

.logo_top {
	margin: 0px;
	padding: 0px;
}
.balken_rahmen {
  border: 1px solid #000;
  background-color: #fff;
  height: 7px;
  width: 104px;
  font-size: 7px;
}
.balken_fuellung {
  background-color: #88ac0b;
  border: 1px solid #88ac0b;
  height: 3px;
  font-size: 3px;
  margin: 1px;
}
.showInfoBox {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 15px;
	z-index: 100;
	text-align: center;
	color: #fff;
	background-color: #000;
	border-bottom: 1px solid #666;
	padding: 8px 2px;
	font-size: 13px;
	font-weight: bold;
}
.showInfoBox a.inText:link, a.inText:visited, a.inText:active {
	color: #eee;
	text-decoration: underline;
}
.showInfoBox a.inText:hover {
	text-decoration: none;
}
.showInfoBox a.closeLink:link, a.closeLink:visited, a.closeLink:hover, a.closeLink:active {
	color: #fff;
	text-decoration: none;
	position: fixed;
	right: 10px;
	top: 6px;
}

div.navBlockLinks {
	padding-bottom: 5px;
}

div.navBlockLinks a, div.navBlockLinks a:link, div.navBlockLinks a:visited {
	display: block;
	border-bottom: 1px solid #eff0f1;
	background-image: url(/images/go.gif);
	background-repeat: no-repeat;
	background-position: 5px center;
	padding: 4px 4px 4px 25px;
	text-align: left;
	font-weight: bold;
	background-color: #fff;
	text-decoration: none;
}

div.navBlockLinks.matchList a, div.navBlockLinks.matchList a:link, div.navBlockLinks.matchList a:visited {
	display: block;
	border-bottom: 1px solid #eff0f1;
    background-image: none;
	padding: 4px 4px 4px 9px;
	text-align: left;
	font-weight: bold;
	background-color: #fff;
	text-decoration: none;
}

div.navBlockLinks a:hover {
	background-color: #f5f5f5;
	text-decoration: none;
}

.pagenava {padding: 2px 6px; border: solid 1px #ddd; text-decoration: none; display: inline; margin-right: 0.5em; padding: 0.25em 1em; font-weight: bold; }
.pagenava:link { background: #fff; text-decoration: none; }
.pagenava:hover { background: #0063dc; color: #fff; text-decoration: none; }
.pagenava.aktiv { background: #0063dc; color: #fff; text-decoration: none; }
.pagenavn {padding: 2px 6px; border: solid 1px #eee; text-decoration: none; display: inline; margin-right: 0.5em; padding: 0.25em 1em; font-weight: normal; }

div#top_box_nav {
	display: block;
	margin: 10px 0 0 0;
	padding: 0;
}
div#top_box_nav a, div#top_box_nav a:link, div#top_box_nav a:visited, div#top_box_nav a:active {
	display: block;
	margin: 1px 0;
	padding: 4px 8px;
	color: #fff;
	text-shadow: 0 1px 1px rgba(0,0,0,0.5);
	font-weight: bold;
}
div#top_box_nav .red {
	background-color: rgb(255,0,0);
}
div#top_box_nav .red:hover {
	background-color: rgba(255,0,0,0.6);
}
div#top_box_nav .blue {
	background-color: rgb(55,103,179);
}
div#top_box_nav .blue:hover {
	background-color: rgba(55,103,179,0.6);
}
div#top_box_nav .green {
	background-color: rgb(130,193,62);
}
div#top_box_nav .green:hover {
	background-color: rgba(130,193,62,0.6);
}
div#top_box_nav .grey {
	background-color: rgb(30,30,30);
}
div#top_box_nav .grey:hover {
	background-color: rgba(30,30,30,0.6);
}
div#top_box_nav .lightgrey {
	background-color: rgb(60,60,60);
}
div#top_box_nav .lightgrey:hover {
	background-color: rgba(60,60,60,0.6);
}
input[type="submit"], a.button {
	display: inline-block;
	min-width: 10em;
	width: auto;
	height: auto;
	margin: 0;
	padding: 3px 6px;
	border: 1px solid #999;
	background: #f3f3f3;
	cursor: pointer;
	background-color: #efefef;
	background-image: linear-gradient(to bottom, #f3f3f3, #eeeeee);
	background-image: -moz-linear-gradient(top, #f3f3f3, #eeeeee);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#f3f3f3), to(#eeeeee));
	background-image: -webkit-linear-gradient(top, #f3f3f3, #eeeeee);
	background-image: -ms-linear-gradient(top, #f3f3f3, #eeeeee);
	background-image: -o-linear-gradient(top, #f3f3f3, #eeeeee);
	background-repeat: repeat-x;
	color: #333;
	text-align: center;
	text-decoration: none;
	border-radius: 3px;
	-webkit-border-radius: 3px;
	-moz-border-radius: 3px;
	transition: all 0.2s ease 0.1s;
	-moz-transition: all 0.2s ease 0.1s;
	-webkit-transition: all 0.2s ease 0.1s;
	-ms-transition: all 0.2s ease 0.1s;
	-o-transition: all 0.2s ease 0.1s;
	-webkit-touch-callout: none;
}
input[type="submit"]:hover, a.button:hover {
	border: 1px solid #666;
	color: #333;
	background-color: #e8e8e8;
	background-image: linear-gradient(to bottom, #eeeeee, #dddddd);
	background-image: -moz-linear-gradient(top, #eeeeee, #dddddd);
	background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#eeeeee), to(#dddddd));
	background-image: -webkit-linear-gradient(top, #eeeeee, #dddddd);
	background-image: -ms-linear-gradient(top, #eeeeee, #dddddd);
	background-image: -o-linear-gradient(top, #eeeeee, #dddddd);
	background-repeat: repeat-x;
}

@media (max-width: 879px) {
	#wrap, #content-wrap, #header, #footer {
		width: auto;
		max-width: 820px;
	}
	#main {
		float: none;
		width: auto;
	}
	#sidebar {
		float: none;
		width: auto;
	}
	#menu {
		overflow: hidden;
	}
	.infokasten {
		width: 42%;
	}
	.balken_rahmen {
		width: 12%;
	}
	.showInfoBox {
		height: auto;
	}
	.showInfoBox a.closeLink:link, .showInfoBox a.closeLink:visited, .showInfoBox a.closeLink:hover, .showInfoBox a.closeLink:active {
		position: relative;
		top: auto;
		right: 2px;
		left: auto;
		bottom: 2px;
		display: block;
		margin: 12px 0;
	}
}

<?php ob_end_flush(); ?>
