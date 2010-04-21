<html>
<head>
<title>Welcome to the User Story Machine!</title>

<style type="text/css">
    img {
        border: 0;
}

.tooltip{
   position: absolute;
   top: 0;
   left: 0;
   z-index: 3;
   display: none;
   width: 800px;
   background: #fff;
   border: 1px solid orange;
   padding: 20px;
}
body {
 background-color: #fff;
 margin: 40px;
 font-family: Lucida Grande, Verdana, Sans-serif;
 font-size: 14px;
 color: #4F5155;
}

a {
 color: #003399;
 background-color: transparent;
 font-weight: normal;
}

h1 {
 color: #444;
 background-color: transparent;
 border-bottom: 1px solid #D0D0D0;
 font-size: 16px;
 font-weight: bold;
 margin: 24px 0 2px 0;
 padding: 5px 0 6px 0;
}

code {
 font-family: Monaco, Verdana, Sans-serif;
 font-size: 12px;
 background-color: #f9f9f9;
 border: 1px solid #D0D0D0;
 color: #002166;
 display: block;
 margin: 14px 0 14px 0;
 padding: 12px 10px 12px 10px;
}
label, input, textarea {
    clear: both;
}
label {
    font-size: 1.8em;
}
.col {
    float: left;
    width: 550px;
    border-right: 1px solid #000;
    padding: 20px;
}
div.error {
    color: #fff;
    font-weight: bold;
    background-color: red;
    padding: 5px;
    font-size: 1.5em;
}
div.story {
    background: lightgrey;
    padding: 5px;
    margin-bottom: 5px;
}
ul, ul li {
    display: inline;
    padding: 0;
    margin: 0;
}
div.message {
    color: #000;
    font-weight: bold;
    background-color: lightgreen;
    padding: 5px;
    font-size: 1.5em;
}
#indexCard {
    width: 800px;
    border: 1px solid #000;
    padding: 10px;
}
#indexCard h1 {
    margin: 0; padding: 0;
    font-size: 2em;
}
@media all
{
	.page-break	{ display:none; }
        .eggs { height: auto; }
}

@media print
{
	.page-break	{ display:block; page-break-before:always; }
        .hidePrint { display: none; }
        .eggs { height: 500px; }
}
label {
    cursor: hand;
    cursor: pointer;
}
label.small {
    font-size: 1.1em;
}
.critical {
    color: #000;
}
.non-critical {
    color: #c3c3c3;
}
</style>
</head>
<body>
    <h1 class="hidePrint">Welcome to the User Story Machine!</h1>
<ul class="hidePrint">
    <li><a href="/">Home</a> |</li>
    <li><a href="/stories">Add a story</a> |</li>
    <li><a href="/stories/allnicks">Show all stories</a> |</li>
    <li><a href="/stories/plan">Plan and prioritise</a> |</li>
    <!-- <li><a href="/prioritise/">Let's prioritise!</a></li> -->
</ul>
