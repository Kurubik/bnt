<?php

namespace App\Plans;

Class Order
{
    public static $orderList = 'payments';
    public static $planID = 'plan';
    public static $simType = 'sim';
    public static $planType = 'plan_types';


    /**
     * @param $db
     * @param array $data
     * @return string
     */
    public static function createOrder($db, $data = array())
    {
        $ipAddress = self::getClientIp();
        $client_id =  time() . '_' . str_replace('.', '', $ipAddress);
        $company = '';

        $legal = 2;
        $individual = 1;

        if (isset($_POST['company']) && !empty($_POST['company']) && $_POST['plan_type'] === $legal) {
            $company = $_POST['company'];
        }

        $sql =
          ('
          INSERT INTO
            '. self::$orderList .'
          SET
            client_id = :client_id,
            name = :name,
            surname = :surname,
            phone = :phone,
            email = :email,
            country = :country,
            city = :city,
            address = :address,
            zip = :zip,
            plan = :plan,
            plan_type = :plan_type,
            sim = :sim,
            company = :company,
            ip_address = :ip,
            success = 0
          ');

        $prep = $db->prepare($sql);

        $prep->bindValue(':client_id', $client_id, 'string');
        $prep->bindValue(':name', $_POST['name'], 'string');
        $prep->bindValue(':surname', $_POST['surname'], 'string');
        $prep->bindValue(':phone', $_POST['phone'], 'string');
        $prep->bindValue(':email', $_POST['email'], 'string');
        $prep->bindValue(':country', $_POST['country'], 'string');
        $prep->bindValue(':city', $_POST['city'], 'string');
        $prep->bindValue(':address', $_POST['address'], 'string');
        $prep->bindValue(':zip', $_POST['zip'], 'string');
        $prep->bindValue(':plan', $_POST['plan'], 'string');
        $prep->bindValue(':plan_type', $_POST['plan_type'], 'integer');
        $prep->bindValue(':sim', $_POST['sim'], 'string');
        $prep->bindValue(':company', $company, 'string');
        $prep->bindValue(':ip', $ipAddress, 'string');

        $prep->execute();

        return $client_id;
    }


    /**
     * @param string $id
     * @param $db
     * @return int
     */
    public static function getPlanPrice($id = '', $db)
    {
        $price = array();

        $sql =
          ('
            SELECT `price`
            FROM '. self::$planID .'
            WHERE id = :id
          ');

        $prep = $db->prepare($sql);
        $prep->bindValue(':id', $id, 'string');
        $prep->execute();
        $price = $prep->fetch();

        return $price['price'];
    }


    /**
     * @return mixed
     */
    public static function getClientIp()
    {
        if ( function_exists( 'apache_request_headers' ) ) {
            $headers = apache_request_headers();
        } else {
            $headers = $_SERVER;
        }

        if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
            $the_ip = $headers['X-Forwarded-For'];
        } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )) {
            $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
        } else {
            $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
        }

        return $the_ip;
    }


    /**
     * @param int $id
     * @param $db
     * @return mixed
     */
    public static function getPlanType($id = 0, $db)
    {
        $sql =
          ('
            SELECT `name`
            FROM '. self::$planType .'
            WHERE id = :id
          ');

        $prep = $db->prepare($sql);
        $prep->bindValue(':id', $id, 'string');
        $prep->execute();
        $name = $prep->fetch();

        return $name['name'];
    }


    public static function orderSuccess($db, $id)
    {
        $sql =
          ('
            UPDATE '. self::$orderList .'
            SET `success` = 1
            WHERE client_id = :id
          ');

        $prep = $db->prepare($sql);
        $prep->bindValue(':id', $id, 'string');

        return $prep->execute();
    }
}
