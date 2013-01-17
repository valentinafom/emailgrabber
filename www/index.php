<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Grabber Email</title>

        <link rel="stylesheet" type="text/css" href="css/parser_main.css" media="all"/>
        <link rel="stylesheet" type="text/css" href="css/index.css" media="all"/>
        <link href="css/selects.css" rel="stylesheet" type="text/css" media="all"/>
    </head>

    <body id="body">
        <div id="main">
            <table width="100%" cellspacing="10" border="0">
                <tr><td align="center" id="title" colspan="2"><h1>Grabber Email</h1></td></tr>
                <tr><td width="30%">
                        <h4 id="h4">Введите запросы, каждый с новой строки.</h4>
                    </td><td class="sett"><h4 id="h4">Настройки</h4>
                    </td></tr>
                <tr><td>
                        <TEXTAREA style="width:100%; height:300px; overflow:hidden;" name="textarea" onFocus="focusTextarea()" onChange="ChangeTextarea()" onBlur="blurTextarea()" id="textarea"></TEXTAREA>
                    </td>
                    <td class="sett">
                        <table>
                            <tr><td>Выберите глубину поиска:</td></tr>
                             <tr><td><!--<input name="depth" id="depth" type="text" size="30" maxlength="3" onFocus="Focus('depth')" onBlur="Blur('depth','50')" value="50"/>-->

                                           <select class="wid200" name="depth" id="depth">
                                                <?php
                                                for ($i = 1; $i <= 50; $i++) {
                                                    $val = $i * 10;
                                                    if ($i == 2) {
                                                        echo'<option value="' . $val . '" selected="selected">' . $val . '</option>';
                                                        continue;
                                                    }
                                                    echo'<option value="' . $val . '">' . $val . '</option>';
                                                }
                                                ?>
                                            </select>
                            </td></tr>
                            <tr><td>Выберите URL поисковика:</td></tr>
                            <tr><td><select class="wid100" name="url" id="url">
                                        <option value="google.ru" selected="selected">google.ru</option>
                                        <option value="google.com">google.com</option>
                                    </select>
                                </td></tr>
                        </table>
                    </td>
                </tr>
                <tr><td>
                        <input type="hidden" name="data" id="data" value=""/>
                        <ul class="buttonUl"><li id="submit" class="button"onClick="SubmitDownAjax();">Поиск</li></ul>
                        <!--<input type="submit" sty name="submit" value="Сохранить" onClick="SaveKeyword();return false;"/>-->
                        <!--<input id="3" style="display:none;" type="submit" name="submit" onClick="SubmitDownAjax();return false;" value="Поиск ajax"/>-->
                        </form>
                    </td>
                </tr>
                <tr><td align="center" colspan="2"><h4 id="h4">Результаты</h4></td></tr>
                <tr>
                    <td colspan="2"><div id="result" align="center">
                            Результатов пока нет    
                        </div>
                    </td>

                </tr>
            </table>
        </div><br />

        <div  id="div" onClick="focusBody();"></div>
        <div id="progressBar" onDblClick="focusPbDouble();">поиск</div>

        </script>
        <script type="text/javascript" src="libraries/index.js"></script>
        <script type="text/javascript" src="libraries/jquery-1.7.1.js"></script>	
        <script type="text/javascript" src="libraries/jquery.json-2.3.js"></script>
        <script type="text/javascript" src="libraries/heightDocument.js"></script>
        <script type="text/javascript" src="libraries/select/jquery.selects.js"></script>
        <script type="text/javascript" src="libraries/select/jsScroll.js"></script>

        <script type="text/javascript">

            jQuery(function($){
                //console.log("работает");
	
                $("li#1_0").bind({"click":function(){
                        $("input:submit#1").click()}});
	
                $(".button").bind({"mousedown":function(){
                        $(this).css({"background":"#2FAA00","color":"white"})}});
	
                $(".button").bind({"mouseup mouseleave":function(){
                        $(this).css({"background":"#A6A6A6","color":"black"})}});	
            })
        </script>	
    </body>
</html>