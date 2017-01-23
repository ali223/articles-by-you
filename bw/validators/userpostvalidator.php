<?php

namespace BW\validators;

use BW\tools\BlogPost;
use BW\tools\BlogPostDB;


class UserPostValidator extends Validator{

   
    public function validatePostForm(Array $userPostForm,  BlogPost $blogPost, Array $userPostImageFile = [], $formType = null) {   

        $errorMessages = [];

   
       if (empty($userPostForm['txtposttitle'])) {
            $errorMessages[] = "Please enter the Article Title";
        } else {
            $blogPost->posttitle = $this->filterInput($userPostForm['txtposttitle']);
        }

        if (empty($userPostForm['txtpostdesc'])) {
            $errorMessages[] = "Please enter the Article Description";
        } else {
            $blogPost->postdesc = $this->filterInput($userPostForm['txtpostdesc']);
        }

        if (empty($userPostForm['txtposttext'])) {
            $errorMessages[] = "Please enter the Article Text";
        } else {
            $blogPost->posttext = $this->filterInput($userPostForm['txtposttext']);
        }

        if ($userPostForm['txtpostisvisible'] == '') {
            $errorMessages[] = "Please enter the if the Article is a Draft or to be published";
        } else {
            $blogPost->postisvisible = $this->filterInput($userPostForm['txtpostisvisible']);
        }

        // store the current date and time as the postdate
        $blogPost->postdate = time();


         $uploadok = 0;

        if ($userPostImageFile['txtpostimage']['error'] == UPLOAD_ERR_OK) {
            $targetdir = "images/";
            $targetfile = $targetdir . basename($userPostImageFile['txtpostimage']['name']);

            // check to make sure that the uploaded file is actually an image file
            $check = getimagesize($userPostImageFile['txtpostimage']['tmp_name']);

            if ($check !== false) {
                //echo "file is an image";
                move_uploaded_file($userPostImageFile['txtpostimage']['tmp_name'], $targetfile);
                $blogPost->postimage = $targetfile;
            } else {
                $errorMessages[] = "The uploaded file is not an image";
            }
        } elseif ($userPostImageFile['txtpostimage']['error'] == UPLOAD_ERR_FORM_SIZE) {
            $errorMessages[] = "Uploaded image file size must be 5mb or less.";
        } elseif ($userPostImageFile['txtpostimage']['error'] == UPLOAD_ERR_NO_FILE) {
            if (($formType == 'new')) {
                $errorMessages[] = "No Article Image was uploaded";
            }
        }

        if ($formType == "edit") {
            if (empty($userPostForm['txtpostid'])) {
                $errorMessages[] = "Please enter the Article Id";
            } else {
                $blogPost->postid = $this->filterInput($userPostForm['txtpostid']);
            }
        }


        return $errorMessages;
    }

}
