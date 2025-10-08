<?php

namespace App\Services;

class CheckFormService
{
  public static function checkGender($contact)
  {
    $gender = $contact->gender === 0 ? '男性' : '女性';
    return $gender;
  }

  public static function checkAge($contact)
  {
    if ($contact->age === 1) {
      $age = '～19歳';
    }
    if ($contact->age === 2) {
      $age = '20歳～29歳';
    }
    if ($contact->age === 3) {
      $age = '30歳～39歳';
    }
    if ($contact->age === 4) {
      $age = '40歳～49歳';
    }
    if ($contact->age === 5) {
      $age = '50歳～59歳';
    }
    if ($contact->age === 6) {
      $age = '60歳～';
    }

    return $age;
  }
}
