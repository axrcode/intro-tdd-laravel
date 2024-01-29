<?php

function mail_validation($email)
{
   $validate = filter_var($email, FILTER_VALIDATE_EMAIL);

   return boolval($validate);
}