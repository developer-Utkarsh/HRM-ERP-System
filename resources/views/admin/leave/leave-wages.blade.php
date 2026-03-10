@extends('layouts.admin')
@section('content')
<style>
tr
	{mso-height-source:auto;}
col
	{mso-width-source:auto;}
br
	{mso-data-placement:same-cell;}
.style0
	{mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	white-space:nowrap;
	mso-rotate:0;
	mso-background-source:auto;
	mso-pattern:auto;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	border:none;
	mso-protection:locked visible;
	mso-style-name:Normal;
	mso-style-id:0;}
td
	{mso-style-parent:style0;
	padding-top:1px;
	padding-right:1px;
	padding-left:1px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:locked visible;
	white-space:nowrap;
	mso-rotate:0;}



.xl73 {	
	border-right:none;
	border-left:.5pt solid windowtext;
	mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;
	}



.xl75
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	background:white;
	mso-pattern:black none;}
.xl76 .xl77 
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	background:white;
	mso-pattern:black none;}

.xl78
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl79
	{mso-style-parent:style0;
	color:windowtext;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	background:white;
	mso-pattern:black none;}
.xl80
	{mso-style-parent:style0;
	color:windowtext;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:left;
	background:white;
	mso-pattern:black none;}
.xl81
	{mso-style-parent:style0;
	color:windowtext;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	background:white;
	mso-pattern:black none;}
.xl82
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:left;
	background:white;
	mso-pattern:black none;
	padding-left:18px;
	mso-char-indent-count:2;}
.xl83
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	background:white;
	mso-pattern:black none;}
.xl84
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Garamond, serif;
	mso-font-charset:0;
	background:white;
	mso-pattern:black none;}
	
.commonOne {
	mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	background:white;
	mso-pattern:black none;
}


.xl85 {
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
}

.xl86{
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
}

.xl87{
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
}

.xl88{	
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
}


.xl89{
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
}


.xl90
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl91
	{mso-style-parent:style0;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl92
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"\[ENG\]\[$-409\]mmm\\-yy\;\@";
	text-align:left;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl93
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"\[ENG\]\[$-409\]dd\\-mmm\;\@";
	text-align:center;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl94
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl95
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	background:white;
	mso-pattern:black none;}
.xl96
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl97
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl98
	{mso-style-parent:style0;
	color:windowtext;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"\[ENG\]\[$-409\]mmm\\-yy\;\@";
	text-align:left;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl99
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl100
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl101
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl102
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl103
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"\[ENG\]\[$-409\]mmm\\-yy\;\@";
	text-align:left;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl104
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl105
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl106
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl107
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl108
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl109
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl110
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:Fixed;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl111
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl112
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl113
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl114
	{mso-style-parent:style0;
	color:blue;
	font-size:10.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl115
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl116
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl117
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl118
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl119
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl120
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl121
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl122
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl123
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl124
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl125
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl126
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl127
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl128
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl129
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl130
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl131
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl132
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl133
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl134
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl135
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl136
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl137
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl138
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl139
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl140
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl141
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	mso-rotate:90;}
.xl142
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	mso-rotate:90;}
.xl143
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	mso-rotate:90;}
.xl144
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	mso-rotate:90;}
.xl145
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	mso-rotate:90;}
.xl146
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl147
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl148
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl149
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl150
	{mso-style-parent:style0;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl151
	{mso-style-parent:style0;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl152
	{mso-style-parent:style0;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl153
	{mso-style-parent:style0;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"Short Date";
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl154
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl155
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl156
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl157
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl158
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl159
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl160
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;}
.xl161
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl162
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:white;
	mso-pattern:black none;}
.xl163
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	background:white;
	mso-pattern:black none;}
.xl164
	{mso-style-parent:style0;
	color:windowtext;
	font-size:15.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	background:white;
	mso-pattern:black none;}
.xl165
	{mso-style-parent:style0;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:left;
	background:white;
	mso-pattern:black none;}
.xl166
	{mso-style-parent:style0;
	color:blue;
	font-size:12.0pt;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	text-align:left;
	border-top:none;
	border-right:none;
	border-bottom:.5pt dashed windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;
	padding-left:27px;
	mso-char-indent-count:3;}
.xl167
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:0;
	text-align:left;
	border-top:.5pt dashed windowtext;
	border-right:none;
	border-bottom:.5pt dashed windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;
	padding-left:18px;
	mso-char-indent-count:2;}
.xl168
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"\[ENG\]\[$-409\]dd\/mmm\/yy\;\@";
	text-align:left;
	border-top:.5pt dashed windowtext;
	border-right:none;
	border-bottom:.5pt dashed windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;
	padding-left:18px;
	mso-char-indent-count:2;}
.xl169
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"dd\\-mmm\\-yyyy";
	text-align:left;
	border-top:.5pt dashed windowtext;
	border-right:none;
	border-bottom:.5pt dashed windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;
	padding-left:27px;
	mso-char-indent-count:3;}
.xl170
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"dd\\-mmm\\-yyyy";
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:.5pt dotted windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;}
.xl171
	{mso-style-parent:style0;
	color:blue;
	font-weight:700;
	font-family:"Comic Sans MS", cursive;
	mso-font-charset:0;
	mso-number-format:"\#\,\#\#0";
	text-align:left;
	border-top:.5pt dashed windowtext;
	border-right:none;
	border-bottom:.5pt dashed windowtext;
	border-left:none;
	background:white;
	mso-pattern:black none;
	padding-left:18px;
	mso-char-indent-count:2;}
.xl172 
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:1.0pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl173
	{mso-style-parent:style0;
	color:windowtext;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}
.xl174
	{mso-style-parent:style0;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:white;
	mso-pattern:black none;
	white-space:normal;
	mso-rotate:90;}

.xl176
	{mso-style-parent:style0;
	font-size:16.0pt;
	font-weight:700;
	font-family:Garamond, serif;
	mso-font-charset:0;
	text-align:center;
	background:white;
	mso-pattern:black none;}
</style>
<div class="app-content content">
	<div class="content-overlay"></div>
	<div class="header-navbar-shadow"></div>
	<div class="content-wrapper">
		<div class="content-header row">
			<div class="content-header-left col-md-9 col-12 mb-2">
				<div class="row breadcrumbs-top">
					<div class="col-12">
						<h2 class="content-header-title float-left mb-0">Leave Wages</h2>
						<div class="breadcrumb-wrapper col-12">
							<ol class="breadcrumb">
								<li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a>
								</li>
								<li class="breadcrumb-item active">Leave Wages</li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-body">
			<!-- Data list view starts -->
			<section id="data-list-view" class="data-list-view-header">
				<div class="card">
					<div class="card-content collapse show">
						<div class="card-body">
							<div class="users-list-filter">
								<form action="{{ route('admin.leave.leave-wages-tab') }}" method="get" name="filtersubmit" target="_blank">
									<div class="row">
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-status">Employee</label>
											<?php $emp_rec = \App\User::where('status', '1')->where('is_deleted', '0')->orderBy('id','desc')->get(); ?>
											<fieldset class="form-group">												
												<select class="form-control select-multiple1 emp_id" name="emp_id">
													<option value="">Select Any</option>
													@if(count($emp_rec) > 0)
														@foreach($emp_rec as $key => $value)
															<option value="{{ $value->id }}"
																@if($value->id == old('emp_id', app('request')->input('emp_id')))
																	selected
																@endif
															>
																{{ $value->name.' ('. $value->register_id .')' }}
															</option>
														@endforeach
													@endif
												</select>	
												@if($errors->has('emp_id'))
													<span class="text-danger">{{ $errors->first('emp_id') }} </span>
												@endif												
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">From</label>
											<fieldset class="form-group">
												<input type="month" name="fdate" class="form-control fdate" value="{{ !empty(old('fdate')) ? old('fdate') : app('request')->input('fdate') }}" id="">
												@if($errors->has('fdate'))
													<span class="text-danger">{{ $errors->first('fdate') }} </span>
												@endif	
											</fieldset>
										</div>
										<div class="col-12 col-sm-6 col-lg-3">
											<label for="users-list-verified">To</label>
											<fieldset class="form-group">
												<input type="month" name="tdate" class="form-control tdate" value="{{ !empty(old('tdate')) ? old('tdate') : app('request')->input('tdate') }}" id="">
												@if($errors->has('tdate'))
													<span class="text-danger">{{ $errors->first('tdate') }} </span>
												@endif	
											</fieldset>
										</div>
										
										<div class="col-12 col-sm-6 col-lg-3">
											<fieldset class="form-group mt-2">		
											<button type="submit" class="btn btn-primary">Search</button>
											<a href="{{ route('admin.leave.leave-wages') }}" class="btn btn-warning">Reset</a>
											</fieldset>
										</div>
									</div>
									
								</form>
							</div>
						</div>
					</div>
				</div>
				
				@if(!empty($leave_wage))
				<div class="table-responsive">
					<table border="0" cellpadding="0" cellspacing="0" width="1344" style="border-collapse:collapse;table-layout:fixed;width:1008pt">
						<colgroup><col width="64" span="21" style="width:48pt"></colgroup>
						<tbody>
							<tr height="20" style="height:15.0pt">
								<td colspan="21" rowspan="2" height="40" class="xl176" width="1344" style="height:30.0pt;
								width:1008pt">UTKARSH CLASSES &amp; EDUTECH PRIVATE LTD., JODHPUR</td>
							</tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt">
								<td colspan="3" rowspan="3" height="66" class="xl78" width="192" style="height:49.5pt;width:144pt">Form No. 16 (Prescribed under Rule 92)<span style="mso-spacerun:yes">&nbsp;</span></td>
								<td colspan="15" class="xl163">REGISTER OF<span style="mso-spacerun:yes">&nbsp;</span></td>
								<td class="xl76">&nbsp;</td>
								<td class="xl77">&nbsp;</td>
								<td class="xl77">&nbsp;</td>
							</tr>
							<tr height="26" style="height:19.5pt">
								<td colspan="15" height="26" class="xl164" style="height:19.5pt">LEAVE WITH WAGES</td>
								<td colspan="3" class="xl83">Part I- Adult</td>
							</tr>
							<tr height="20" style="height:15.0pt">
								<td colspan="15" height="20" class="xl79" style="height:15.0pt">(As Amended
								up-to-date)</td>
								<td colspan="3" class="xl83">Part II- Children</td>
							</tr>
							<tr height="20" style="height:15.0pt">
								<td height="20" class="xl78" width="64" style="height:15.0pt;width:48pt">&nbsp;</td>
								<td class="xl78" width="64" style="width:48pt">&nbsp;</td>
								<td class="xl78" width="64" style="width:48pt">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl80">&nbsp;</td>
								<td class="xl81">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td class="xl79">&nbsp;</td>
								<td></td>
								<td></td>
							</tr>
							<tr height="26" style="height:19.5pt">
								<td colspan="3" height="26" class="xl165" style="height:19.5pt">Name of Employee</td>
								<td colspan="8" class="xl166">{{ !empty($leave_wage->name) ? $leave_wage->name : '' }}</td>
								<td class="xl82" colspan="3" style="mso-ignore:colspan">Father's/Husband Name</td>
								<td class="xl83">&nbsp;</td>
								<td colspan="6" class="xl166">{{ !empty($leave_wage->user_details) ? $leave_wage->user_details->fname : '' }}</td>
							</tr>
							<tr height="24" style="height:18.0pt">
								<td height="24" class="xl84" colspan="4" style="height:18.0pt;mso-ignore:colspan">S.No. in the Register of Adult/ Child worker<span style="mso-spacerun:yes">&nbsp;</span></td>
								<td colspan="7" class="xl167">{{ !empty($leave_wage->register_id) ? $leave_wage->register_id : '' }}</td>
								<td class="xl82" colspan="3" style="mso-ignore:colspan">Date of entry into service</td>
								<td class="xl83">&nbsp;</td>
								<td colspan="6" class="xl169">{{ !empty($leave_wage->user_details) ? date("d-M-Y", strtotime($leave_wage->user_details->joining_date)) : '' }}</td>
							</tr>
							<tr height="24" style="height:18.0pt">
								<td height="24" class="xl83" colspan="2" style="height:18.0pt;mso-ignore:colspan">Date
								of Discharge</td>
								<td class="xl83">&nbsp;</td>
								<td colspan="8" class="xl170"></td>
								<td class="xl82" colspan="6" style="mso-ignore:colspan">Date and amount of
								payment made in lieu of leave due</td>
								<td class="xl83">&nbsp;</td>
								<td colspan="3" class="xl171">&nbsp;</td>
							</tr>
							<tr height="21" style="height:15.75pt">
								<td height="21" class="xl83" colspan="3" style="height:15.75pt;mso-ignore:colspan">Note :- Separate page will be allo<span style="display:none">ted to each worker.</span></td>
								<td></td>
								<td class="xl75">&nbsp;</td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
							<tr height="20" style="height:15.0pt">
								<td rowspan="11" height="221" class="xl172" width="64" style="border-bottom:1.0pt solid black;height:165.75pt;width:48pt">Calendar Year of service</td>
								<td colspan="2" rowspan="9" class="xl154" width="128" style="border-right:.5pt solid black;	width:96pt">Wages <br>Period</td>
								<td colspan="2" rowspan="9" class="xl154" width="128" style="border-right:.5pt solid black;	width:96pt">Wages earned during the wages period</td>
								<td colspan="4" rowspan="3" class="xl158" width="256" style="width:192pt">Number of	days<br>
								<span style="mso-spacerun:yes">&nbsp;</span>worked during the Calendar year</td>
								<td rowspan="11" class="xl117" width="64" style="border-bottom:1.0pt solid black;width:48pt">Total of cols. 4 to 7</td>
								<td colspan="2" rowspan="3" class="xl158" width="128" style="width:96pt">Leave to	credit</td>
								<td rowspan="11" class="xl117" width="64" style="border-bottom:1.0pt solid black;width:48pt">Total of col. 9 and 10</td>
								<td rowspan="11" class="xl122" width="64" style="border-bottom:1.0pt solid black;width:48pt">Whether leave in accordance with scheme under section <br>
								79 (8) refused</td>
								<td colspan="2" rowspan="9" class="xl126" width="128" style="border-right:.5pt solid black;
								width:96pt">Leave <br>
								enjoyed</td>
								<td rowspan="11" class="xl117" width="64" style="border-bottom:1.0pt solid black;
								width:48pt">Balance of leave on credit</td>
								<td rowspan="11" class="xl117" width="64" style="border-bottom:1.0pt solid black;
								width:48pt">Normal rate of wages</td>
								<td rowspan="11" class="xl133" width="64" style="border-bottom:1.0pt solid black;
								width:48pt">Cash equivalent of advantage accuring through concessional sale
								of food grains &amp; other articals</td>
								<td rowspan="11" class="xl133" width="64" style="border-bottom:1.0pt solid black;
								width:48pt">Rate of wages for the leave period (Total of cols. 15 &amp; 16)</td>
								<td rowspan="11" class="xl141" style="border-bottom:1.0pt solid black">Remarks</td>
							</tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt">
								<td rowspan="8" height="161" class="xl135" width="64" style="border-bottom:1.0pt solid black;
								height:120.75pt;border-top:none;width:48pt">Number of days of work performed</td>
								<td rowspan="8" class="xl135" width="64" style="border-bottom:1.0pt solid black;
								border-top:none;width:48pt">Number of days of lay off</td>
								<td rowspan="8" class="xl135" width="64" style="border-bottom:1.0pt solid black;
								border-top:none;width:48pt">Number of days of maternity leave</td>
								<td rowspan="8" class="xl135" width="64" style="border-bottom:1.0pt solid black;
								border-top:none;width:48pt">Number of days of leave enjoyed</td>
								<td rowspan="8" class="xl135" width="64" style="border-bottom:1.0pt solid black;
								border-top:none;width:48pt">Balance of leave from proceeding year</td>
								<td rowspan="8" class="xl135" width="64" style="border-bottom:1.0pt solid black;
								border-top:none;width:48pt">Leave earned during the year mentioned in col. 1</td>
							</tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt"></tr>
							<tr height="20" style="height:15.0pt">
								<td rowspan="2" height="41" class="xl161" style="border-bottom:1.0pt solid black;
								height:30.75pt">From</td>
								<td rowspan="2" class="xl161" style="border-bottom:1.0pt solid black">To</td>
								<td rowspan="2" class="xl161" style="border-bottom:1.0pt solid black">Rs.</td>
								<td rowspan="2" class="xl161" style="border-bottom:1.0pt solid black">P.</td>
								<td rowspan="2" class="xl146" style="border-bottom:1.0pt solid black">From</td>
								<td rowspan="2" class="xl146" style="border-bottom:1.0pt solid black">To</td>
							</tr>
							<tr height="21" style="height:15.75pt"></tr>
							<tr height="21" style="height:15.75pt">
								<td height="21" class="xl85 commonOne" style="height:15.75pt;border-top:none">1</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">2</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">&nbsp;</td>
								<td colspan="2" class="xl148" style="border-right:.5pt solid black;border-left:
								none">3</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">4</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">5</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">6</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">7</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">8</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">9</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">10</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">11</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">12</td>
								<td colspan="2" class="xl148" style="border-right:.5pt solid black;border-left:
								none">13</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">14</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">15</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">16</td>
								<td class="xl86 commonOne" style="border-top:none;border-left:none">17</td>
								<td class="xl87 commonOne" style="border-top:none;border-left:none">18</td>
							</tr>
							<tr height="23" style="height:17.25pt">
								<td height="23" class="xl88 commonOne" style="height:17.25pt">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td colspan="2" class="xl150" style="border-right:.5pt solid black;border-left:
								none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl90" style="border-left:none">0</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td colspan="2" class="xl152" style="border-right:.5pt solid black;border-left:
								none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl89" style="border-left:none">&nbsp;</td>
								<td class="xl91" style="border-left:none">&nbsp;</td>
							</tr>
							
							
							@if(!empty($fdate) && !empty($tdate))
							@php
								$ts1 = strtotime($fdate.'-01');
								$ts2 = strtotime($tdate.'-01');

								$year1 = date('Y', $ts1);
								$year2 = date('Y', $ts2);

								$month1 = date('m', $ts1);
								$month2 = date('m', $ts2);

								$diff = (($year2 - $year1) * 12) + ($month2 - $month1);
								
							@endphp
							
							@if($diff >= 0)
								@for ($i = 0; $i <= $diff; $i++)
								@php
									$first_selected_month = date('Y-m-d', strtotime("+$i months", strtotime($fdate)));
									
									$year_of_service = date("M-Y", strtotime($first_selected_month.'-01'));
									$first_date = date("d-M", strtotime($first_selected_month.'-01'));
									$last_date = date("t-M", strtotime($first_selected_month.'-01'));
								@endphp
								<tr height="24" style="height:18.0pt">
									<td height="24" class="xl92" style="height:18.0pt;border-top:none">{{$year_of_service}}</td>
									<td class="xl93" style="border-top:none;border-left:none">{{$first_date}}</td>
									<td class="xl93" style="border-top:none;border-left:none">{{$last_date}}</td>
									<td colspan="2" class="xl109" style="border-right:.5pt solid black;border-left:
									none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">0</td>
									<td class="xl95">&nbsp;</td>
									<td class="xl96" style="border-top:none">0</td>
									<td class="xl96" style="border-top:none;border-left:none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">&nbsp;</td>
									<td colspan="2" class="xl111" style="border-right:.5pt solid black;border-left:
									none">&nbsp;</td>
									<td class="xl96" style="border-top:none;border-left:none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">0</td>
									<td class="xl94" style="border-top:none;border-left:none">&nbsp;</td>
									<td class="xl94" style="border-top:none;border-left:none">&nbsp;</td>
									<td class="xl97" style="border-top:none;border-left:none">&nbsp;</td>
								</tr>
								@endfor
							@endif
							@endif
							
							
							
							<tr height="25" style="height:18.75pt">
								<td height="25" class="xl98" style="height:18.75pt;border-top:none">&nbsp;</td>
								<td class="xl99" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl99" style="border-top:none;border-left:none">&nbsp;</td>
								<td colspan="2" class="xl113" style="border-right:.5pt solid black;border-left:
								none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl101" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl101" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl101" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td colspan="2" class="xl115" style="border-right:.5pt solid black;border-left:
								none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl100" style="border-top:none;border-left:none">&nbsp;</td>
								<td class="xl102" style="border-top:none;border-left:none">&nbsp;</td>
							</tr>
							<tr height="25" style="height:18.75pt">
								<td height="25" class="xl103" style="height:18.75pt">&nbsp;</td>
								<td class="xl104" style="border-left:none">&nbsp;</td>
								<td class="xl104" style="border-left:none">&nbsp;</td>
								<td colspan="2" class="xl73" style="border-right:.5pt solid black;border-left:
								none">0</td>
								<td class="xl104" style="border-left:none">0</td>
								<td class="xl104" style="border-left:none">0</td>
								<td class="xl104" style="border-left:none">0</td>
								<td class="xl104" style="border-left:none">0</td>
								<td class="xl104" style="border-left:none">0</td>
								<td class="xl105" style="border-left:none">0</td>
								<td class="xl105" style="border-left:none">0</td>
								<td class="xl105" style="border-left:none">0</td>
								<td class="xl104" style="border-left:none">0</td>
								<td colspan="2" class="xl107" style="border-right:.5pt solid black;border-left:
								none">&nbsp;</td>
								<td class="xl105" style="border-left:none">0</td>
								<td class="xl104" style="border-left:none">&nbsp;</td>
								<td class="xl104" style="border-left:none">&nbsp;</td>
								<td class="xl104" style="border-left:none">&nbsp;</td>
								<td class="xl106" style="border-left:none">&nbsp;</td>
							</tr>
							<tr height="0" style="display:none">
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
								<td width="64" style="width:48pt"></td>
							</tr>
						</tbody>
					</table>
				</div>   
				@endif
                
			</section>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('.select-multiple1').select2({
		placeholder: "Select Any",
		allowClear: true
	});
	$('.select-multiple2').select2({
		placeholder: "Select Any",
		allowClear: true
	});
});

</script>
@endsection
