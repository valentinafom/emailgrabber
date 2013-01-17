<?php
//variables for sample_db
$dbhost='localhost';
$dbusername='root';
$dbuserpassword='';
$default_dbname='default_db';
$MYSQL_ERRNO='';
$MYSQL_ERROR='';

//variables for userviewer
$records_per_page=5;
$user_tablename='user';
$access_log_tablename='access_log';
$new_win_width=600;
$new_win_height=400;

function html_header()
    {global $new_win_width, $new_win_height;?>
	<html><head>
	<script language="javascript" type="text/javascript">
	<!--
	function open_window(url)
	    {var NEW_WIN=null;
		NEW_WIN=window.open ("","RecordViewer");
		NEW_WIN.location.href=url;
		}
	//-->
	</script><title>Сценарий просмотра базы данных</title>
	<!--("","RecordViewer,"toolbar=no, width="+<?php echo $new_win_width;?>+",
		height="+<?php echo $new_win_height;?>+",
		directories=no,status=no"+",scrollbars=yes,resize=no,menubar=no");-->
	</head>
	<body>
	<?php	
	}
function html_footer()
    {?></body></html><?php
	}
//functions for sample_db
function db_connect($dbname='')
    {global $dbhost,$dbusername,$dbuserpassword,$default_dbname;
	global $MYSQL_ERRNO,$MYSQL_ERROR;
	$link_id=mysql_connect($dbhost,$dbusername,$dbuserpassword);
	
	if(!$link_id) 
	    {$MYSQL_ERRNO=0; $MYSQL_ERROR="Не удалось подключится к узлу $dbhost.";
		return 0;
		}	
	else if(!empty($dbname)&&!mysql_select_db($dbname))
	    {$MYSQL_ERRNO=mysql_errno();
		$MYSQL_ERROR=mysql_error();
		return 0;		
		}
	/*else if(empty($dbname)&&!mysql_select_db($defaul_dbname))
	    {$MYSQL_ERRNO=mysql_errno();$MYSQL_ERROR=mysql_error();
		return 0;
		}*/
	else return $link_id;
	}
function sql_error()
    {global $MYSQL_ERRNO,$MYSQL_ERROR;
	if(empty($MYSQL_ERROR))
	    {$MYSQL_ERRN=mysql_errno();$MYSQL_ERROR=mysql_error();
		}
	return "$MYSQL_ERRNO: $MYSQL_ERROR";
	}
function error_message($msg)
    {html_header();
	echo "<script>alert(\"Error: $msg\");history.go(-1)</script>";
	html_footer();
	exit;
	}
function enum_options($field, $link_id)
    {$query="SHOW COLUMNS FROM user LIKE '$field'";
	$result=mysql_query($query,$link_id);
	$query_data=mysql_fetch_array($result);
	if(preg_match("('.*')",$query_data["Type"],$match))
	    {$enum_str=preg_replace("/'/","",$match[0]);
		$enum_options=explode(',',$enum_str);
		return $enum_options;
		}
	else
	    {return 0;
		}
	}
	
?>