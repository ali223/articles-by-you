<?php

namespace app\models;

use PDOException;
use app\validators\FilterInputTrait;


class BlogUserDB {
    use FilterInputTrait;

  private $mydb;

  public function __construct(Database $db) {
      $this->mydb = $db->getConnection();
  }

  public function userExists($username) {
       try{
           $stmt = $this->mydb->prepare("select username from blog_users where username=?");
           $result = $stmt->execute(array($username));
           if($stmt->rowCount()){
               return true;
           }
           
       } catch (PDOException $pe) {
           error_log("<br /> error occurred " . $pe->getMessage());
           return false;
       }
   }

  public function authenticateUser($username, $password) {
         try{
             $stmt = $this->mydb->prepare("select username from blog_users where username=:pusername and userpassword = :ppassword");

              $stmt->bindValue(":pusername",$username);
              $stmt->bindValue(":ppassword",$password);
         
             $result = $stmt->execute();

             return $stmt->rowCount();
             
         } catch (PDOException $pe) {
             error_log("<br /> error occurred " . $pe->getMessage());
             return false;
         }
             
                      
    }
     public function addUser(BlogUser $blogUser) {

         try{
             $stmt = $this->mydb->prepare("insert into blog_users"
                     . "(username, userfirstname, userlastname , userurl , useremail, userregdate,userpassword)"
                     . "values(:pusername, :puserfirstname, :puserlastname , :puserurl , :puseremail, :puserregdate,:puserpassword)");
             
             $stmt->bindValue(":pusername", $blogUser->userName);
             $stmt->bindValue(":puserfirstname", $blogUser->userFirstName);
             $stmt->bindValue(":puserlastname", $blogUser->userLastName);
             $stmt->bindValue(":puserurl", $blogUser->userUrl);
             $stmt->bindValue(":puseremail", $blogUser->userEmail);
             $stmt->bindValue(":puserregdate", $blogUser->userRegDate);
             $stmt->bindValue(":puserpassword", $blogUser->userPassword);
             
             $result= $stmt->execute();
             return $stmt->rowCount();
         }catch(PDOException $pe){
             error_log("<br /> error occurred " . $pe->getMessage());
             return false;
         } 
           
     }
     
     
 public function getUserById($userid) {

   if(!isset($userid)) return false;

  try{ 

        $stmt = $this->mydb->prepare("select * from blog_users where userid = :puserid");
        $stmt->bindValue(":puserid", $userid);


        $stmt->execute();

        if(!$stmt->rowCount()) return false;

        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        $row = $stmt->fetch();

        $blogUser = $this->createBlogUserObjectFromArray($row);

        return $blogUser;

      }catch(PDOException $pe) {
             error_log("<br /> Error occurred " . $pe->getMessage());
      }        
}
     
  public function getUserByUsername($username) {
      if(!isset($username)) return false;

      try{ 

            $stmt = $this->mydb->prepare("select * from blog_users where username = :pusername");
            $stmt->bindValue(":pusername", $username);


            $stmt->execute();

             if(!$stmt->rowCount()) return false;

            $stmt->setFetchMode(\PDO::FETCH_ASSOC);
   
            $row = $stmt->fetch();

            $blogUser = $this->createBlogUserObjectFromArray($row);
            return $blogUser;

      }catch(PDOException $pe) {
             error_log("<br /> Error occurred " . $pe->getMessage());
      }
 }
     
     
    public function updateUser(BlogUser $blogUser) {
        try{
           $stmt = $this->mydb->prepare("update blog_users "
                   . "set userfirstname = :puserfirstname, userlastname = :puserlastname , userurl = :puserurl,"
                   . "useremail = :puseremail where username=:pusername");
           
           $stmt->bindValue(":puserfirstname",$blogUser->userFirstName);
           $stmt->bindValue(":puserlastname",$blogUser->userLastName);
           $stmt->bindValue(":puserurl",$blogUser->userUrl);
           $stmt->bindValue(":puseremail",$blogUser->userEmail);
           $stmt->bindValue(":pusername",$blogUser->userName);
           
           $result= $stmt->execute();
           return $stmt->rowCount();
         }catch(PDOException $pe){
             error_log("<br /> error occurred " . $pe->getMessage());
             return false;
         } 
           
     }
 
  public function updatePassword($username, $userpassword) {

         try{
           $stmt = $this->mydb->prepare("update blog_users "
                   . "set userpassword= :puserpassword"
                   . " where username=:pusername");
           
           $stmt->bindValue(":puserpassword",$userpassword);
           $stmt->bindValue(":pusername",$username);
           
           $result= $stmt->execute();
           return $stmt->rowCount();
         }catch(PDOException $pe){
             error_log("<br /> error occurred " . $pe->getMessage());
             return false;
         } 
    }

    protected function createBlogUserObjectFromArray(Array $userArray) {
          $blogUser = new BlogUser();
       
          $blogUser->userId = $this->filterInput($userArray['userid']);
          $blogUser->userName = $this->filterInput($userArray['username']);
          $blogUser->userFirstName = $this->filterInput($userArray['userfirstname']);
          $blogUser->userLastName = $this->filterInput($userArray['userlastname']);
          $blogUser->userType = $this->filterInput($userArray['usertype']);
          $blogUser->userUrl = $this->filterInput($userArray['userurl']);            
          $blogUser->userEmail = $this->filterInput($userArray['useremail']);            
          $blogUser->userRegDate = $this->filterInput($userArray['userregdate']);            
          $blogUser->userPhoto =$this->filterInput($userArray['userphoto']);           

          return $blogUser;

    }
}