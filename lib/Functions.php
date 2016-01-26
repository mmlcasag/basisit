<?php

class Functions {

    static function debug($value) {
        echo '<script>alert("' . $value . '");</script>';
    }

    static function clean($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);

        return $data;
    }

    static function isEmpty($mx_value) {
        if ($mx_value == '00:00:00')
            return true;
        if (empty($mx_value))
            return true;
        if (is_null($mx_value))
            return true;
        if ($mx_value == "")
            return true;
        if (strlen($mx_value) <= 0)
            return true;

        return false;
    }

    static function isNumeric($mx_value) {
        $mx_value = Functions::clean($mx_value);
        $mx_value = str_replace(',', '.', $mx_value);

        if (!is_numeric($mx_value))
            return false;

        return true;
    }

    static function isInteger($mx_value) {
        $mx_value = Functions::clean($mx_value);

        if (!Functions::isNumeric($mx_value))
            return false;
        if (preg_match('/[[:punct:]&^-]/', $mx_value) > 0)
            return false;

        return true;
    }

    static function isFloat($number) {
        $number = Functions::clean($number);

        if (!Functions::isNumeric($mx_value))
            return false;
        if (!preg_match('/^[\-+]?[0-9]*\,?[0-9]+$/', $number))
            return false;

        return true;
    }

    static function isEmail($email) {
        $email = Functions::clean($email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            return false;

        return true;
    }

    static function isURL($url) {
        $url = Functions::clean($url);

        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url))
            return false;

        return true;
    }

    static function isDate($data) {
        $data = Functions::clean($data);

        if (!preg_match('/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/', $data))
            return false;

        return true;
    }

    static function isTime($hora) {
        $hora = Functions::clean($hora);

        $ok = true;

        if (preg_match('/^[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $hora)) {
            if (substr($hora, 0, 2) > "23")
                $ok = false;
            if (substr($hora, 3, 2) > "59")
                $ok = false;
            if (substr($hora, 6, 2) > "59")
                $ok = false;
        } else if (preg_match('/^[0-9]{2}:[0-9]{2}$/', $hora)) {
            if (substr($hora, 0, 2) > "23")
                $ok = false;
            if (substr($hora, 3, 2) > "59")
                $ok = false;
        } else
            $ok = false;

        return $ok;
    }

    static function isDateTime($dataHora) {
        $dataHora = Functions::clean($dataHora);

        $data = substr($dataHora, 0, 10);
        if (!Functions::isDate($data))
            return false;

        $hora = substr($dataHora, 11, 5);
        if (!Functions::isTime($hora))
            return false;

        return true;
    }

    static function onlyNumbers($st_data) {
        return preg_replace("([[:punct:]]|[[:alpha:]]| )", '', $st_data);
    }

    static function onlyString($st_string) {
        return addslashes(strip_tags($st_string));
    }

    static function toDate($data) {
        if (Functions::isEmpty($data))
            return "";

        $ano = substr($data, 0, 4);
        $mes = substr($data, 5, 2);
        $dia = substr($data, 8, 2);

        $data = date("d/m/Y", mktime(0, 0, 0, $mes, $dia, $ano));

        if ($ano == "0000")
            return "";

        return $data;
    }

    static function toDateToSql($data) {
        if (Functions::isEmpty($data))
            return "";

        $dia = substr($data, 0, 2);
        $mes = substr($data, 3, 2);
        $ano = substr($data, 6, 4);

        return date("Y-m-d", mktime(0, 0, 0, $mes, $dia, $ano));
    }

    static function toDateTime($data) {
        if (Functions::isEmpty($data))
            return "";

        $ano = substr($data, 0, 4);
        $mes = substr($data, 5, 2);
        $dia = substr($data, 8, 2);
        $hor = substr($data, 11, 2);
        $min = substr($data, 14, 2);

        $data = date("d/m/Y H:i", mktime($hor, $min, 0, $mes, $dia, $ano));

        if ($ano == "0000")
            return "";

        return $data;
    }

    static function toDateTimeToSql($data) {
        if (Functions::isEmpty($data))
            return "";

        $dia = substr($data, 0, 2);
        $mes = substr($data, 3, 2);
        $ano = substr($data, 6, 4);
        $hor = substr($data, 11, 2);
        $min = substr($data, 14, 2);

        $data = date("Y-m-d H:i", mktime($hor, $min, 0, $mes, $dia, $ano));

        if ($ano == "0000")
            return "";

        return $data;
    }

    static function toTime($time) {
        if (Functions::isEmpty($time))
            return "00:00:00";
        
        $time = str_replace(":", "", $time);
        
        $ss = substr($time, strlen($time) - 2, 2);
        $mm = substr($time, strlen($time) - 4, 2);
        $hh = substr($time, 0, strlen($time) - 4);

        if ($ss >= 60) {
            $ss = $ss - 60;
            $mm = $mm + 1;
        }

        if ($mm >= 60) {
            $mm = $mm - 60;
            $hh = $hh + 1;
        }

        $ss = str_pad($ss, 2, '0', STR_PAD_LEFT);
        $mm = str_pad($mm, 2, '0', STR_PAD_LEFT);
        $hh = str_pad($hh, 2, '0', STR_PAD_LEFT);

        return $hh . ':' . $mm . ":" . $ss;
    }
    
    function sumTimes($time1, $time2) {
        $times = array($time1, $time2);
        $seconds = 0;

        foreach ($times as $time) {
            list($hour, $minute, $second) = explode(':', $time);
            $seconds += $hour * 3600;
            $seconds += $minute * 60;
            $seconds += $second;
        }

        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        if ($seconds <= 9)
            $seconds = "0" . $seconds;
        if ($minutes <= 9)
            $minutes = "0" . $minutes;
        if ($hours <= 9)
            $hours = "0" . $hours;

        return "{$hours}:{$minutes}:{$seconds}";
    }
    
    static function encrypt($st_string) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qEncoded = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), $st_string, MCRYPT_MODE_CBC, md5(md5($cryptKey))));

        return $qEncoded;
    }

    static function decrypt($st_string) {
        $cryptKey = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($cryptKey), base64_decode($st_string), MCRYPT_MODE_CBC, md5(md5($cryptKey))), "\0");

        return $qDecoded;
    }
    
    static function email($to, $subject, $message) {
        $sendgrid = new SendGrid("basisit", "B4s1sIT#2014");
        $email    = new SendGrid\Email();
        
        $email->addTo($to)
              ->setFrom("site@basisit.com.br")
              ->setFromName('Chamados - BasisIT')
              ->setSubject($subject)
              ->setHtml($message);
        
        $sendgrid->send($email);
    }
    
    static function getParametro($nome) {
        $connection = Databases::connect();
        
        $model = new ParametrosModel();
        $parametroVo = $model->loadByName($connection, $nome);
        
        Databases::disconnect($connection);
        
        return $parametroVo->getValor();
    }
    
}