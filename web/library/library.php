<?php
require_once 'autoload.php';

    function startSession($isUserActivity=true, $prefix=null) {
    $sessionLifetime = 900; //15 минут сессия
    $idLifetime = 60;

    if ( session_id() ) return true;
    session_name('KIAREMOTO'.($prefix ? '_'.$prefix : ''));
    ini_set('session.cookie_lifetime', $sessionLifetime);
    if ( ! session_start() ) return false;

    $t = time();

    if ( $sessionLifetime ) {
        if ( isset($_SESSION['lastactivity']) && $t-$_SESSION['lastactivity'] >= $sessionLifetime ) {
            destroySession();
            return false;
        }
        else {
            if ( $isUserActivity ) $_SESSION['lastactivity'] = $t;
        }
    }

    if ( $idLifetime ) {
        if ( isset($_SESSION['starttime']) ) {
            if ( $t-$_SESSION['starttime'] >= $idLifetime ) {
                session_regenerate_id(true);
                $_SESSION['starttime'] = $t;
            }
        }
        else {
            $_SESSION['starttime'] = $t;
        }
    }

    return true;
}

function destroySession() {
    if ( session_id() ) {
        session_unset();
        setcookie(session_name(), session_id(), time()-60*60*24);
        session_destroy();
    }
}

	function http_request_kia($url,$data='',$login='',$password='',$post=false) {
	    $result = FALSE;
	    if ($post) {
		    $header=array('Content-Type: application/json',                                                                                
			          'Content-Length: ' . strlen($data),
			          'apikey: NmQ2Y2U2ZTJhY2NlNWVkNTdhYjRmN2RlMmNiYjFkM2RhZGE2NzU5NA==');	
	    } else 
	    {
		    $header=array('Content-Type: application/json',                                                                                
			          'apikey: NmQ2Y2U2ZTJhY2NlNWVkNTdhYjRmN2RlMmNiYjFkM2RhZGE2NzU5NA==');	
	    }
	    if(!empty($url)) {
	        if(function_exists('curl_init')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
		    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			if ($post) {
			        curl_setopt($curl, CURLOPT_HEADER, TRUE);
  			        curl_setopt($curl, CURLOPT_POST, $post);
			        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			}
		    curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, "$login:$password");
                //curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		        //curl_setopt($curl, CURLOPT_COOKIE,'kiatmpfile.tmp');
		        //curl_setopt($curl, CURLOPT_COOKIEJAR,'kiatmpfile.tmp');
		        //curl_setopt($curl, CURLOPT_COOKIEFILE,'kiatmpfile.tmp');
			curl_setopt($curl, CURLOPT_USERAGENT, 'Kia-Remoto/880 CFNetwork/672.1.15 Darwin/14.0.0');
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

			$result = curl_exec($curl);
			//echo('>>>>>'.$result.'<br>');
			$info = curl_getinfo( $curl,CURLINFO_HTTP_CODE);
            if (curl_errno($curl)) {
                    print curl_error($curl).'<br>';
            }
            //print_r(array(curl_getinfo( $curl)),array(curl_error ($curl)) );
			//echo('<br>End process');
			curl_close($curl);
			if ( ($result<>null) and ($info=200) ) {
				//echo('<br>Запрос успешно выполен<br>');
	   	        return $result;
			} else {
                //echo('<br>Запрос не выполен!!!<br>');
                return $result;
            }
		}
	}
	}

function http_request_kia_login($login='',$password='') {
    $result = false;
    $http = http_request_kia('https://rmt.brightbox.ru/api/v2/users/me','',$login,$password);
    $res = json_decode($http);
    //echo prettyPrint($http);
    if ( ($res!=null) and ($res->{'User'}->{'Phone'}==$login) ) {
        return TRUE;
    }

}

function prettyPrint( $json )
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}



