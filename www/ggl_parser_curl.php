<?php

//-----------------Функция для записи результатов в массив------------------------
function save_result($data) {
    global $resultAdress;
    $resultAdress = array_merge((array) $resultAdress, (array) $data);
}

//-----------------Функция для выполнения запроса------------------------
function curl($url_str, $url) {
    global $endUrl, $countCurl;
    $countCurl++;
    $host = "Host: www." . $url;

    $curl = curl_init($url_str);

    $header = array('User-Agent: Opera/9.80 (Windows NT 5.1; U; en) Presto/2.2.15 Version/10.10',
        $host,
        'Accept: text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1',
        'Accept-Language: ru-RU,ru;q=0.9,en;q=0.8',
        'Accept-Charset: iso-8859-1, utf-8, utf-16, *;q=0.1',
        'Connection: Keep-Alive, TE',
        'TE: deflate, gzip, chunked, identity, trailers',
        'Content-Type: application/x-www-form-urlencoded');
    curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl, CURLOPT_HEADER, 0); //При установке этого параметра в ненулевое значение результат будет включать полученные заголовки.
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);   // переходит по редиректам
    curl_setopt($curl, CURLOPT_TIMEOUT, 6); // Задает масимальное время выполнения операции в секундах
    curl_setopt($curl, CURLOPT_ENCODING, "gzip, deflate"); // тип сжатия данных
    curl_setopt($curl, CURLOPT_AUTOREFERER, true); //TRUE для автоматической установки поля Referer: в запросах, перенаправленных заголовком Location: 
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //При установке этого параметра в ненулевое значение CURL будет возвращать результат, а не выводить его.
    curl_setopt($curl, CURLOPT_COOKIESESSION, 1); //TRUE to mark this as a new cookie "session". It will force libcurl to ignore all cookies it is about to load that are "session cookies" from the previous session.

    $response = curl_exec($curl);

    $endUrl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

    if (!$response) {
        /* $error = curl_error($curl) . '(' . curl_errno($curl) . ')';
          echo $error; */
    } else {
        //print($response);		
    }
    curl_close($curl);

    return $response;
}

//----------------------функция поиска адресов----------------------
/*Граббит главную страницу сайта на емэйл и страницу контактов
 * страница контактов передается в parserSite()
 */
function searchAdress($site, $keyword) {
    $url_str = "http://www." . $site;
    $response = curl($url_str, $site);

    preg_match_all('/<meta[^>]+charset=([^"\'>]+)[^>]*[\/]?>/i', $response, $matches);  //Coding
    if (count($matches[0]) == 0) {
        return array($keyword, $site, null, null, null);
    }
    $coding = strtolower($matches[1][0]);
    if ($coding != 'utf-8') {
        $response = iconv($coding, 'utf-8', $response);
    }
    preg_match_all('/<title>([^>]+?)<\/title>/iu', $response, $matches);  //Title

    $title = $matches[1][0];

    $myRegExpEmail = '/>[^<]*?([\da-zа-я][a-zа-я\d-_\.]+@[a-zа-я\d-\.]+)/iu'; //email in tag
    $myRegExpEmailHref = '/(?:mailto:|mailto: |mail-to:|mail-to: )([a-zа-я\d][a-zа-я\d-_\.]+@[a-zа-я\d-\.]+)/iu'; //email in href

    preg_match_all($myRegExpEmail, $response, $matches);  //Email on main page
    $email = array_unique($matches[1]);

    preg_match_all($myRegExpEmailHref, $response, $matches);  //Email on main page in href
    $emailHref = array_unique($matches[1]);

    $email = array_unique(array_merge((array) $email, (array) $emailHref));

    $myRegExpContact = '/<a[^>]*href=(?:"|\')(?:http:\/\/)?([^>]+?)(?:"|\')[^>]*>(?:<img)?[^>]*(?:>)?(?<!<\/a>)(?<=>)(?:контакты|contacts|связь).*(?=<\/a>)/iu'; //Href page contact
    preg_match_all($myRegExpContact, $response, $matches);

    $contact = $matches[1][0];

    //Парсим страницу контактов----------------------------------------
    if ($contact) {
        $result = parserSite($site, $contact, $myRegExpEmail, $myRegExpEmailHref, $coding);
        $url = $result[1];
        $email = array_merge((array) $email, (array) $result[0]);
    } else {
        $contactUrl = array('kontakty', 'contacts', 'contact', 'kontakt', 'kontakts', 'контакты');
        $def = array('html', 'php');
        $bool = 0;
        foreach ($contactUrl as $value) {
            if ($bool == 1)
                break;
            foreach ($def as $val) {
                $url_str = "http://www." . $site . '/' . $value . '.' . $val;
                $response = curl($url_str, $site);

                if ($coding != 'utf-8') {
                    $response = iconv($coding, 'utf-8', $response);
                }
                if ($response) {
                    //парсим контактную страницу и мыло
                    //Может быть обработка урлов

                    preg_match_all($myRegExpContact, $response, $matches);
                    $contact = $matches[1][0];

                    if ($contact) {
                        $result = parserSite($site, $contact, $myRegExpEmail, $myRegExpEmailHref, $coding);
                        $url = $result[1];
                        $bool = 1;
                        break;
                    } else {
                        preg_match_all($myRegExpEmail, $response, $matches);  //Email on second page
                        $result = array_unique($matches[1]);

                        preg_match_all($myRegExpEmailHref, $response, $matches);  //Email on main page in comments
                        $emailHref = array_unique($matches[1]);

                          $email = array_unique(array_merge((array) $email, (array) $emailHref));

                        if (count($result) > 0) {
                            $url = $site . '/' . $value . '.' . $val;
                            $bool = 1;
                            $result = array($result); //parserSite возвращает такой же тип
                            break;
                        }
                    }
                }
                else
                    continue;
            }
        }
        $email = array_merge((array) $email, (array) $result[0]);
    }
    $email = array_unique($email);
    //-----------------------------------------------------------------        
    return array($keyword, $site, $title, $url, $email);
}

function parserSite($site, $contact, $myRegExpEmail, $myRegExpEmailHref, $coding) {//Поиск ящиков на странице контактов
    $contact = preg_replace("/^\//", "", $contact);
    $contact = preg_replace("/^(www.)/", "", $contact);
    $contact = preg_replace("/^" . $site . "(\/)?/", "", $contact);

    $url = $site . '/' . $contact;

    $url_str = "http://www." . $url;
    $response = curl($url_str, $site);

    if ($coding != 'utf-8') {
        $response = iconv($coding, 'utf-8', $response);
    }

    preg_match_all($myRegExpEmail, $response, $matches);  //Adress on main page
    $email = array_unique($matches[1]);

    preg_match_all($myRegExpEmailHref, $response, $matches);  //Email on main page in comments
    $emailHref = array_unique($matches[1]);

    $email = array_unique(array_merge((array) $email, (array) $emailHref));

    return array($email, $url);
}

//----------------------Функция анализатор---------------------------
/*Заходит на страницы выдачи парсит адреса сайтов и 
 * адреса следующих страниц гугл
 * передает сайты в searchAdress()
 * вызвращает адреса следующих страниц выдачи
 * или 0 если их нет 
 * 
 */
function parser($url, $url_str, $keyword) {
    global $sites;

    $response = curl($url_str, $url);

    preg_match_all('/<h3 class=["]?r["]?><a href="http:\/\/([^>]+?)"[^>]+?>/', $response, $matches);  //URL of site
    preg_match_all('/<td><a href="([^>]+?)" class=["]?fl["]?>/', $response, $matches_nav);  //URL of page
    preg_match_all('/<td><a href="[^>]+?" class=["]?fl["]?><span [^>]+?><\/span>(\d{1,2})<\/a><\/td>/', $response, $page_nav);  //Number of page

    if (count($matches[0]) == 0) {

        preg_match_all('/<input type="hidden" name="id" value="([^>]+?)">/i', $response, $matches);  //Value of Input.id
        $id = $matches[1][0];
        preg_match_all('/<input type="hidden" name="continue" value="([^>]+?)">/i', $response, $matches);  //Value of Input.continue
        $continue = $matches[1][0];
        if ($id && $continue) {// т.е страница с капчей
            echo 0;
            exit;
        } else {
            $statistic = array($keyword, 'Sites not found', null, null, null);
            save_result($statistic);
            return 0;
        }
    }

    foreach ($matches_nav[1] as $i => $v) {
        $href_nav[$page_nav[1][$i]] = "http://www." . $url . $v;
    }
    foreach ($matches[1] as $v) {
        preg_match('/^(www.)?([^\/]+)/i', $v, $matchesSite);
        
        $punycode = $matchesSite[2];
        $idn = new idna_convert(array('idn_version'=>2008));
        $punycode = (stripos($punycode, 'xn--')!==false) ? $idn->decode($punycode) : $idn->encode($punycode);
        
        $href[] = $punycode;
    }

    //удаляем отпарсеные сайты
    $href = array_diff($href, $sites);
    $href = array_unique($href);
    //Поиск адресов по сайтам

    foreach ($href as $value) {
        $statistic[] = searchAdress($value, $keyword);
    }
    save_result($statistic);

    $sites = array_merge((array) $sites, (array) $href);

    if (is_array($href_nav))
        return $href_nav;
    else {
        $statistic = array($keyword, 'Sites not found', null, null, null);
        save_result($statistic);
        return 0;
    }
}

//-------------------------Main function--------------------------
/*Перебор ключевых запросов
 * передача параметров в iteration()
 */
function Main($depth, $url, $keywords) {

    foreach ($keywords as $value) {
        //-----------------Определяем переменные-------------------
        //$str_query = str_replace(" ","+",$value);
        $str_query = urlencode($value);
        $url_str = "http://www." . $url . "/search?q=" . $str_query . "&sclient=psy-ab&hl=ru";
        //---------------------------------------------------------

        iteration($depth, $url, $url_str, $value);

        //Очищаем переменную $sites - тк новый запрос
        $sites = array();
    }
}
/*Обход страниц выдачи 
 * стоит ограничение по глубине поиска
 * передача параметров в parser()
 */
function iteration($depth, $url, $url_str, $keyword) {

    for ($i = 0; $i < $depth; $i++) {

        $response = parser($url, $url_str, $keyword);
        if ($response == 0) {
            //Если результаты на одной странице и совпадений нет
            return 0;
        }
        $j = $i + 2;
        $last_key = array_pop(array_keys($response));
        if ($last_key < $j) {
            //Все доступные страницы гуггл просмотрены
            return 0;
        }


        $url_str = str_replace("&amp;", "&", $response[$j]);
    }
}

?>