<?php

namespace BW\tools;





class BlogUserDB {

private $mydb;





  public function __construct(Database $db) {

           $this->mydb = $db->getConnection();

  }

    

    public static function filter($data){

        $data = trim($data);        

        $data = htmlspecialchars($data);

        return $data;

    }




     public function userExists($username) {
         try{
             $stmt = $this->mydb->prepare("select username from blog_users where username=?");
             $result = $stmt->execute(array($username));
             if($stmt->rowCount()){
                 return true;
             }
             
         } catch (PDOException $pe) {
             echo "<br /> error occurred " . $pe->getMessage();
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
             echo "<br /> error occurred " . $pe->getMessage();
             return false;
         }
             
                      
    }
     public function addUser(BlogUser $user) {

             // echo "<pre>", print_r($user), "</pre>";
         try{
           $stmt = $this->mydb->prepare("insert into blog_users"
                   . "(username, userfirstname, userlastname , userurl , useremail, userregdate,userpassword)"
                   . "values(:pusername, :puserfirstname, :puserlastname , :puserurl , :puseremail, :puserregdate,:puserpassword)");
           
           $stmt->bindValue(":pusername",$user->username);
           $stmt->bindValue(":puserfirstname",$user->userfirstname);
           $stmt->bindValue(":puserlastname",$user->userlastname);
           $stmt->bindValue(":puserurl",$user->userurl);
           $stmt->bindValue(":puseremail",$user->useremail);
           $stmt->bindValue(":puserregdate",$user->userregdate);
           $stmt->bindValue(":puserpassword",$user->userpassword);
           
           $result= $stmt->execute();
           return $stmt->rowCount();
         }catch(PDOException $pe){
             echo "<br /> error occurred " . $pe->getMessage();
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

            $buser = new BlogUser();

            $buser->userid = self::filter($row['userid']);

            $buser->username = self::filter($row['username']);

            $buser->userfirstname = self::filter($row['userfirstname']);

            $buser->userlastname = self::filter($row['userlastname']);

            $buser->usertype = self::filter($row['usertype']);

            $buser->userurl = self::filter($row['userurl']);
            
            $buser->useremail = self::filter($row['useremail']);
            
            $buser->userregdate = self::filter($row['userregdate']);
            
            $buser->userphoto =self::filter($row['userphoto']);
            
            

          return $buser;

           

   }catch(\PDOException $pe) {

         echo "<br /> Error occurred " . $pe->getMessage();

    }


         
     }


//////////////////////////////////////////////////////////////////////
     
     
     
     public function getUserByUsername($username) {
               if(!isset($username)) return false;

  try{ 

         $stmt = $this->mydb->prepare("select * from blog_users where username = :pusername");

        $stmt->bindValue(":pusername", $username);


        $stmt->execute();

      if(!$stmt->rowCount()) return false;

        $stmt->setFetchMode(\PDO::FETCH_ASSOC);
   

 

       $row = $stmt->fetch();

            $buser = new BlogUser();

            $buser->userid = self::filter($row['userid']);
            
            //$buser->userpassword = self::filter($row['userpassword']);

            $buser->username = self::filter($row['username']);

            $buser->userfirstname = self::filter($row['userfirstname']);

            $buser->userlastname = self::filter($row['userlastname']);

            $buser->usertype = self::filter($row['usertype']);

            $buser->userurl = self::filter($row['userurl']);
            
            $buser->useremail = self::filter($row['useremail']);
            
            $buser->userregdate = self::filter($row['userregdate']);
            
            $buser->userphoto =self::filter($row['userphoto']);
            
            

          return $buser;

           

   }catch(\PDOException $pe) {

         echo "<br /> Error occurred " . $pe->getMessage();

    }


         
     }
     
     
     public function updateUser(BlogUser $user) {

             // echo "<pre>", print_r($user), "</pre>";
         try{
           $stmt = $this->mydb->prepare("update blog_users "
                   . "set userfirstname = :puserfirstname, userlastname = :puserlastname , userurl = :puserurl,"
                   . "useremail = :puseremail where username=:pusername");
           
           $stmt->bindValue(":puserfirstname",$user->userfirstname);
           $stmt->bindValue(":puserlastname",$user->userlastname);
           $stmt->bindValue(":puserurl",$user->userurl);
           $stmt->bindValue(":puseremail",$user->useremail);
           $stmt->bindValue(":pusername",$user->username);
           
           $result= $stmt->execute();
           return $stmt->rowCount();
         }catch(PDOException $pe){
             echo "<br /> error occurred " . $pe->getMessage();
             return false;
         } 
           
     }
 
 public function updatePassword($username, $userpassword) {

             // echo "<pre>", print_r($user), "</pre>";
         try{
           $stmt = $this->mydb->prepare("update blog_users "
                   . "set userpassword= :puserpassword"
                   . " where username=:pusername");
           
           $stmt->bindValue(":puserpassword",$userpassword);
           $stmt->bindValue(":pusername",$username);
           
           $result= $stmt->execute();
           return $stmt->rowCount();
         }catch(PDOException $pe){
             echo "<br /> error occurred " . $pe->getMessage();
             return false;
         } 
           
     }
 
  

    







}