<?php

namespace BW\validators;

use BW\tools\BlogComment;


class CommentValidator{
    use FilterInputTrait;

    public function validateCommentForm(Array $commentForm, BlogComment $blogComment) {

        $errorMessages = [];


        if (empty($commentForm['txtcommentname'])) {
            $errorMessages[] = "Please enter your name";
        } else {
            $blogComment->commentName = $this->filterInput($commentForm['txtcommentname']);
        }

        if (empty($commentForm['txtcommenttext'])) {
            $errorMessages[] = "Please enter your comment";
        } else {
            $blogComment->commentText = $this->filterInput($commentForm['txtcommenttext']);
        }

        if (empty($commentForm['txtcommentpostid'])) {
            $errorMessages[] = "Please enter your comment post id";
        } else {
            $blogComment->commentPostId = $this->filterInput($commentForm['txtcommentpostid']);
        }

        
        $blogComment->commentDate = time();
        $blogComment->commentIsVisible = 1;

        if (!empty($errorMessages)) {
            return $errorMessages;
        }


    }

}
