<?php

namespace Tests\Unit\Services;

use App\Services\CheckFormService;
use Tests\TestCase;

class CheckFormServiceTest extends TestCase
{
    /**
     * checkGender: 性別が0の場合は「男性」を返すことを確認
     */
    public function test_check_gender_returns_male_for_zero()
    {
        $contact = (object) ['gender' => 0];
        $result = CheckFormService::checkGender($contact);

        $this->assertEquals('男性', $result);
    }

    /**
     * checkGender: 性別が1の場合は「女性」を返すことを確認
     */
    public function test_check_gender_returns_female_for_one()
    {
        $contact = (object) ['gender' => 1];
        $result = CheckFormService::checkGender($contact);

        $this->assertEquals('女性', $result);
    }

    /**
     * checkAge: 年齢区分1の場合は「～19歳」を返すことを確認
     */
    public function test_check_age_returns_under_19()
    {
        $contact = (object) ['age' => 1];
        $result = CheckFormService::checkAge($contact);

        $this->assertEquals('～19歳', $result);
    }

    /**
     * checkAge: 年齢区分2の場合は「20歳～29歳」を返すことを確認
     */
    public function test_check_age_returns_20s()
    {
        $contact = (object) ['age' => 2];
        $result = CheckFormService::checkAge($contact);

        $this->assertEquals('20歳～29歳', $result);
    }

    /**
     * checkAge: 年齢区分3の場合は「30歳～39歳」を返すことを確認
     */
    public function test_check_age_returns_30s()
    {
        $contact = (object) ['age' => 3];
        $result = CheckFormService::checkAge($contact);

        $this->assertEquals('30歳～39歳', $result);
    }

    /**
     * checkAge: 年齢区分4の場合は「40歳～49歳」を返すことを確認
     */
    public function test_check_age_returns_40s()
    {
        $contact = (object) ['age' => 4];
        $result = CheckFormService::checkAge($contact);

        $this->assertEquals('40歳～49歳', $result);
    }

    /**
     * checkAge: 年齢区分5の場合は「50歳～59歳」を返すことを確認
     */
    public function test_check_age_returns_50s()
    {
        $contact = (object) ['age' => 5];
        $result = CheckFormService::checkAge($contact);

        $this->assertEquals('50歳～59歳', $result);
    }

    /**
     * checkAge: 年齢区分6の場合は「60歳～」を返すことを確認
     */
    public function test_check_age_returns_60_and_over()
    {
        $contact = (object) ['age' => 6];
        $result = CheckFormService::checkAge($contact);

        $this->assertEquals('60歳～', $result);
    }
}
