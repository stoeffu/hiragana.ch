<?php

class User {

  private $nickname;
  private $email;
  private $registered;

  // private constructor - obejcts can be retrieved via static methods below
  private function __construct ($nickname, $email) {
    $this->nickname = $nickname;
    $this->email = $email;
  }

  public function getNickname () {
    return $this->nickname;
  }

  public function getEmail () {
    return $this->email;
  }

  public function setEmail ($email) {
    $this->email = $email;
  }

  public function save () {
    $sql = sprintf (" UPDATE user
                      SET email='%s'
                      WHERE nickname='%s'",
                      $this->email, $this->nickname);
    $res = DB::doQuery($sql);
    return $res != null;
  }


  // static functions

  public static function getUserByNickname ($nickname) {
    $sql = "SELECT * FROM user WHERE nickname = '$nickname'";
    $res = DB::doQuery($sql);

    if (!$res) return null;
    return $res->fetch_object(get_class());
  }

  public static function createUser ($nickname, $email, $password) {

    // first check if user nickname already exists
    $sql = "SELECT nickname FROM user WHERE nickname = '$nickname'";
    $res = DB::doQuery($sql);
    if (mysqli_num_rows($res)>1) {
      FileFunctions::log("User already exists in DB");
      return false;
    }


    // create new user
    $pwdData = Auth::createLogin($password);
    $hash=$pwdData['hash'];
    $salt=$pwdData['salt'];
    $timestamp = date('Y-m-d H:i:s');
    FileFunctions::log("Nickname=$nickname / Email=$email / Hash=$hash / Salt=$salt / Timestamp=$timestamp");

    $sql = sprintf("INSERT INTO user
                    VALUES ('%s', '%s', '%s', '%s', '%s')",
                    $nickname, $email, $pwdData['hash'], $pwdData['salt'], $timestamp);
    $res = DB::doQuery($sql);
    if (!isset($res) || $res==null) {
      $mysqlError = mysql_error();
      FileFunctions::log("User could not be created in DB [$mysqlError]");
      return false;
    }


    // create user object and return it
    return new User ($nickname, $email);
  }

  public static function deleteUser ($nickname) {
    $sql = "DELETE FROM user WHERE nickname = '$nickname'";
    $res = DB::doQuery($sql);

    return $res != null;
  }



}