<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Grabber Email</title>

        <link rel="stylesheet" type="text/css" href="css/parser_main.css" media="all"/>
        <link href="css/selects.css" rel="stylesheet" type="text/css" />
        <style type="text/css">
            textarea, select, input [type="text"]{font-family:Arial, Helvetica, sans-serif;}
            textArea{ border: 1px solid #bdbcbd; margin: 0px; overflow:hidden; -moz-border-radius:6px;
    -webkit-border-radius:6px;
    border-radius:6px;}
            input{text-align:center; margin-right:10px;margin-left:0px;border-radius: 3px;}
            .sett{padding-left: 80px;}
            #resultTable{background-color: white;}
            #resultTable td{border: 1px solid grey;}
            #resultTable table td{border: none;}
            #resultTable {border-collapse: collapse;}
            #resultTable table {border-collapse: collapse;margin: 0;}
            #h4{display:inline;font-weight:400; color:#31D231;}
            #Settings{position:absolute; left:500px;top:100px;}
            #progressBar{padding-bottom:30px; padding-top:30px; position:absolute; top:45%; left:45%; text-align:center; ruby-align:center; background-color:#31D231;
            width:150px; height:30px; visibility:hidden; z-index:2;border-radius: 6px;}
            #div{position:absolute; top:0; left:0; width:100%; height:100%; z-index:1;visibility:hidden;opacity: 0.5; background:#666;}
            #topTable{background-color:#FFF;border-collapse:collapse; margin-bottom:10px;}
            #topTable td{padding:3px;}
            #main table{
                padding-bottom:15px;
            }
            td{vertical-align:central;}
            table{background-color:#EAFBEC;}

            li#submit{
                margin-left:70px;
            }

        </style>
    </head>

    <body id="body">
        <script type="text/javascript">
            //автоматически глобальная переменная
            var DataArray = new Array();
            var Sites = new Array();
            var bool = 0; // 1 - поиск завершен
            var iteration=0;
            var id=0; //идентификаторы таймеров
            var id1=0;
            var move=0;
            var change = 0;// новые запросы не вводились
            var selectChange = 0;// новый сайт из списка не выбирался 
            //Используется в SaveTextarea как текущий элемент input site
            var indexSelect=0;
            
            function SaveTextarea(){
                //Сохраняем данные из textarea в DataArray
                var text = document.getElementById("textarea").value;	
						
                var array = new Array();
                var MyRegExp = /[\w-_А-Яа-я ]+/g;
                //массив из запросов
                array = text.match(MyRegExp);	
                var j;
                var k;
                for(j in array){
                    DataArray[j]=array[j];
                }			
            }
            function SubmitDown(){
                //$.toJSON() из библиотеки library\jquery.json-2.3.js(плагин к jquery)
                // toJSON с ассоциативными массивами неработает
	
                //DataArray кодируется в JSON и присваивается скрытому полю data
		
                SaveTextarea();
                
                var encoded = $.toJSON(DataArray);
                var hidden = document.getElementById("data");
                hidden.value = encoded;			
            }
            function doAnimation(){
                /**
                 *Меняет прогресс бар непосредственно
                 */
                if(bool==1){
                    clearInterval(id);
                    return;
                }
                if(iteration==0){
                    document.getElementById("progressBar").innerHTML = '&nbsp;поиск.';
                    iteration++;
                    return;
                }
                if(iteration==1){
                    document.getElementById("progressBar").innerHTML = '&nbsp;&nbsp;поиск..';
                    iteration++;
                    return;
                }
                if(iteration==2){
                    document.getElementById("progressBar").innerHTML = '&nbsp;&nbsp;&nbsp;поиск...';
                    iteration++;
                    return;
                }
                if(iteration==3){
                    document.getElementById("progressBar").innerHTML = 'поиск';
                    iteration=0;
                    return;
                }	
	
            }
            function SubmitDownAjax(){
                /*
                 *Выполняется при нажатии на кнопку "поиск"
                 *передает данные скрипту посредствоя аякс
                 *ждет ответа, ответ выводит в элементе result
                 **/
                bool=0;
                id = setInterval("doAnimation()", 400);
                document.getElementById("progressBar").innerHTML = 'поиск';
                
                SubmitDown();
                 
                //document.getElementById("progressBar").style.visibility = 'visible';
                //document.getElementById("div").style.visibility = 'visible';
                jQuery(function($){
                    $("#progressBar").css({"visibility":"visible","opacity":"0"}).fadeTo(300,1);
                    $("#div").css({"visibility":"visible","opacity":"0"}).fadeTo(300,0.5);
                })
	
                var data = document.getElementById("data").value;
                var url = document.getElementById("url").value;   
                var depth = document.getElementById("depth").value; 
	
                var url = "parser_curl.php?submit=" + 1 + "&data=" + data + "&url=" + url+ "&depth=" + depth;
            	
                /* var request = new HttpRequest(url,SubmitDownAjax_callBack);
                request.send();*/
                $.ajaxSetup({
                    "type":"Get",
                    "url":url,
                    "success":function(sResponseText){
                        bool=1;
                        if(sResponseText==0){
                            document.getElementById("progressBar").innerHTML = 'Парсер заблокирован google.';
                        }
                        else{
                            document.getElementById("progressBar").innerHTML = 'Поиск завершен.';
                            document.getElementById("result").innerHTML = sResponseText;
                            document.getElementById("div").style.height = getDocumentHeight()+'px';
                        }
                    }                
                });
                $.ajax();
            }
            function SubmitDownAjax_callBack(sResponseText){
                bool=1;
                var MyRegExp = /\d{1,2}:\d{1,2}:\d{1,2}/;
	
                if(sResponseText==1){
                    document.getElementById("progressBar").innerHTML = 'Произошла ошибка';		
                }
                if(sResponseText==0){
                    document.getElementById("progressBar").innerHTML = 'Парсер заблокирован google.';
                }
                if(sResponseText==2){
                    document.getElementById("progressBar").innerHTML = 'За последние 12 часов поиск уже осуществлялся.';		
                }
                if(MyRegExp.test(sResponseText)){
                    document.getElementById("progressBar").innerHTML = 'Превышено максимально допустимое число обращений к поисковой системе. Счетчик обнулиться через '+sResponseText;		
                }
                if(sResponseText!=0&&sResponseText!=1&&sResponseText!=2&&MyRegExp.test(sResponseText)!=true){
                    document.getElementById("progressBar").innerHTML = 'Поиск завершен.';
                    document.getElementById("result").innerHTML = sResponseText;
                    document.getElementById("div").style.height = getDocumentHeight()+'px';
                } 
            }
            function focusBody(){
                /*Выполняется при клике на первый слой за прогрессбаром
                 *убирает прогресс бар
                 **/
                if(bool==1&&move==0){
                    //document.getElementById("progressBar").style.visibility = 'hidden';
                    //document.getElementById("div").style.visibility = 'hidden';
                    jQuery(function($){
                        $("#progressBar").fadeOut(300);
                        $("#div").fadeOut(300);
                    })
                }
            }
            function focusPbDouble(){
            /*Выполняется при 2-ом клике на прогресс баре
             *убирает прогрессбар
             **/
                if(bool==1&&move==0){
                    jQuery(function($){
                        $("#progressBar").fadeOut(300);
                        $("#div").fadeOut(300);
                    })
                }
            }

        </script>

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
                                        <option value="google.ru">google.ru</option>
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