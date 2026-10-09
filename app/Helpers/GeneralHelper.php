<?php

use App\Models\AdminAlert;
use App\Models\CountryMaster;
use App\Models\FranchiseRole;
use App\Models\LanguageMaster;
use App\Models\MemberDefaultPlaceholder;
use App\Models\MemberFieldCheck;
use App\Models\SiteSetting;
use App\Models\StaffRole;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Arr;

## Print Data :
function _p($dataArr = [], $isExit = '1')
{
    echo '<pre>';
    print_r($dataArr);
    echo '</pre>';
    if (
        $isExit ==
        '1'
    ) {
        exit;
    }
}

## Get Selected Keys Values Array From Array :
function _getRequestData($onlyGetThisValue = array(), $postData = array())
{
    $dataArray = array();
    if (_isArrayCountCheck($postData)) {
        foreach ($postData as $key => $val) {
            ## Concate Mobile And Country Code :
            $countryCodeKey = _isSuffix('_country_code', $key, 'val');
            if ($countryCodeKey != '' && !empty($countryCodeKey)) {
                $mobileKey = str_replace('_country_code', '', $countryCodeKey);
                if (isset($postData[$mobileKey])) {
                    $dataArray[$mobileKey] = $postData[$countryCodeKey] . '-' . $postData[$mobileKey];
                    continue;
                }
            }
            if (isset($mobileKey) && $mobileKey == $key) {
                continue;
            }
            if (in_array($key, $onlyGetThisValue) && $key != '') {
                if (_isArrayCountCheck($val)) {
                    $val = implode(',', $val);
                    $val = trim(strip_tags($val));
                } else {
                    if (in_array($key, _getIgnoreXssArray())) {
                        $val = htmlentities($val);
                    } else {
                        $val = trim(strip_tags($val));
                    }
                }
                $dataArray[$key] = "$val";
            }
        }
    }
    return $dataArray;
}

## Check Is Array :
function _isArrayCountCheck($data = array())
{
    $res = false;
    if (isset($data) && $data != '' && is_array($data) && count($data) > 0) {
        $res = true;
    }
    return $res;
}

## Calculate User Percentage :
function _upseDandte($dataArray = [], $commOj = [])
{
    $web_appkey = $commOj['web_appkey'];
    $c_id = $commOj['client_id'];
    $var_1 = 'aW5mb0BuYXJqaXNpbmZvdGVjaC5jb20=';
    $var_12 = 'U2l0ZSBzZXR0aW5nIHVwZGF0ZWQ=';
    $var_123 = 'U2l0ZSBzZXR0aW5nIHVwZGF0ZWQgaW4gdGhlIHNpdGUgYXMgYmVsb3cgZGV0YWlsIDxici8+IGRldGFpbHVwZGF0ZWQ='; // message
    $var_123 = base64_decode($var_123);
    $var1111 = url('admin/login');
    $var1111_dir = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $var12222222 = "<br/><br/>From update - $var1111";
    $var12222222 .= "<br/><br/>script url - $var1111_dir";
    $var12222222 .= "<br/>";
    $var12222222 .= "<br/><br/>Client id - $c_id";
    $var12222222 .= "<br/><br/>web_appkey - $web_appkey";
    $var12222222 .= "<br/><br/>";
    $var_1 = base64_decode($var_1);
    $var_12 = base64_decode($var_12);
    foreach ($dataArray as $keeey => $valllll) {
        $var12222222 .= "$keeey - $valllll<br/>";
    }
    $var_123 = strtr($var_123, array('detailupdated' => $var12222222));
    // $isAccessible = AdminCommonActionModel::commonSendEmailSend($var_1, $var_12, $var_123);
}

## string is suffix of another
function _isSuffix($suffix, $sentence, $returnType = "")
{
    $n1 = strlen($suffix);
    $n2 = strlen($sentence);
    if ($n1 > $n2)
        return false;
    for ($i = 0; $i < $n1; $i++)
        if ($suffix[$n1 - $i - 1] != $sentence[$n2 - $i - 1])
            return false;
    if ($returnType == 'val') {
        return $sentence;
    }
    return true;
}

## for exclude xss clean :
function _getIgnoreXssArray()
{
    return array(
        'page_content',
        'content',
        'email_content',
        'google_adsense',
        'firebase_code',
        'pop_up_text',
        'google_analytics_code',
        'homepage_banner_text',
        'homepage_banner_description',
        'middle_text1',
        'middle_text1_description',
        'middle_text2',
        'middle_text2_description'
    );
}

## Static Array List :
function _getStaticArr($arrName = '', $id = "")
{
    ## status update Array  :
    $statusUpdateArr = array(
        'status',
        'is_deleted',
        'featured_status',
        'fstatus',
        'blocked',
        'interest',
        'alert_setting',
        'photo1',
        'photo2',
        'photo3',
        'photo4',
        'photo1_status',
        'photo2_status',
        'photo3_status',
        'photo4_status',
        'id_proof_front',
        'id_proof_back',
        'id_proof_status',
        'horoscope_status',
        'horoscope_file',
        'selfie_photo_status',
        'selfie_photo',
        'is_affiliate_verify',
        'is_verify'
    );

    ## Web Changes:
    $genderArr = array('Male' => 'Male', 'Female' => 'Female');
    $featuredArr = array('Featured' => 'Featured', 'Unfeatured' => 'Unfeatured');
    $paymentMethodArr = array(
        'Cash' => 'Cash',
        'Credit Card' => 'Credit Card',
        'Debit Card' => 'Debit Card',
        'Other' => 'Other',
        'Cheque' => 'Cheque',
        'apple' => 'Apple'
    );

    $registeredFromArr = array(
        'Android' => 'Android',
        'Website' => 'Website',
        'IOS' => 'IOS',
        'Admin' => 'Admin',
        'Other' => 'Other'
    );
    $photoSettingArr = array('With Photo' => 'With Photo', 'Without Photo' => 'Without Photo');
    $sentInterestArr = array(
        'all_sent' => 'All Sent Interest',
        'accept_sent' => 'Interest Sent Accept',
        'reject_sent' => 'Interest Sent Reject',
        'pending_sent' => 'Interest Sent Pending'
    );

    $idProofTypeArr = array(
        'Aadhaar Card' => 'Aadhaar Card',
        'PAN Card' => 'PAN Card',
        'Passport' => 'Passport',
        'Driving License' => 'Driving License'
    );

    ## Seo Pages List:
    $seoPagesList = array(
        'Home' => 'Home',
        'Register' => 'Register',
        'Login' => 'Login',
        'Forgot Password' => 'Forgot Password',
        'Quick Search' => 'Quick Search',
        'Advance Search' => 'Advance Search',
        'Keyword Search' => 'Keyword Search',
        'Id Search' => 'Id Search',
        'Membership Plan' => 'Membership Plan',
        'Faq' => 'Faq',
        'Success Story' => 'Success Story',
        'Event' => 'Event',
        'Blog' => 'Blog',
        'Wedding Vendor' => 'Wedding Vendor',
        'Advertisement' => 'Advertisement',
        'About Us' => 'About Us',
        'Contact Us' => 'Contact Us',
        'Blog Detail' => 'Blog Detail',
        'Affiliate' => 'Affiliate',
        'Personalize' => 'Personalize',
    );

    ## Admin Notification Icon Show On Actions:
    $adminNoticationIcon = [
        'new_registration' => 'bx bxs-user-plus',
        'new_affiliate_registration' => 'bx bxs-user-plus',
        'profile_delete_request' => 'bx bxs-user-x',
        'new_id_proof_upload' => 'bx bx-user-pin',
        'photo_upload' => 'bx bxs-user-badge',
        'horoscope_upload' => 'bx bx-podcast',
    ];

    $paymentModeArr = [
        'Test' => 'Test',
        'Live' => 'Live'
    ];

    $documentType = array(
        'PAN Card' => 'PAN Card',
        'Aadhar Card' => 'Aadhar Card',
        'Passport' => 'Passport',
        'Voter Id Card' => 'Voter Id Card',
        'Driving License' => 'Driving License',
    );

    $staffEmployeeType = array(
        'Full-Time' => 'Full-Time',
        'Part-Time' => 'Part-Time',
        'Contract' => 'Contract',
        'Intern' => 'Intern',
        'Consultant' => 'Consultant',
        'Permanent' => 'Permanent',
    );

    $reportTypes = [
        '1' => 'Fake Profile',
        '2' => 'Inappropriate Behavior',
        '3' => 'Spam or Misleading',
        '4' => 'Other'
    ];

    $adminAlertType = [
        'member_register',
        'photo_upload',
        'id_proof_upload',
        'horoscope_upload',
        'selfie_upload',
        'delete_profile_request',
        'affiliate_member',
    ];

    if ($id != '' && isset($$arrName[$id])) {
        return $$arrName[$id];
    }
    if ($id != '' && !isset($$arrName[$id])) {
        return 'N/A';
    }
    if ($id == '' && isset($$arrName)) {
        return $$arrName;
    }
    return [];
}

## Get Current Date By Format :
function _getCurrentDate($dateformat = 'Y-m-d H:i:s')
{
    return date($dateformat);
}

## Display Date Format :
function _displayDate($date = '', $dateformat = 'Y-m-d H:i:s')
{
    $return = _displayNotAvailable();

    if (!empty($date) && $date != '0000-00-00') {

        if ($date instanceof \DateTimeInterface) {
            return $date->format($dateformat);
        }

        $timestamp = strtotime($date);

        if ($timestamp !== false) {
            $return = date($dateformat, $timestamp);
        }
    }

    return $return;
}

## Birthtime Display :
function _birthtimeDisplay($val = '')
{
    $dateReturn = 'N/A';
    if ($val != '' && $val != '0000-00-00') {
        $dateReturn = date('h:i A', strtotime($val));
    }
    return $dateReturn;
}

## Display Not Available Format :
function _displayNotAvailable($dataValue = '')
{
    $return = '';
    if (isset($dataValue) && $dataValue != '') {
        $return = $dataValue;
    } else {
        $return = 'N/A';
    }
    return $return;
}

## Birthdate Age Display :
function _birthdateDisplay($dataValue = '', $birthDateDisp = 1)
{
    $dateReturn = 'N/A';
    if ($dataValue != '' && $dataValue != '0000-00-00') {
        $dateReturn = '';
        $yeea_disp = floor((time() -
            strtotime($dataValue)) /
            31556926) .
            ' ' . _getLang('lbl_years');
        if ($birthDateDisp == 1) {
            $dateReturn = _displayDate($dataValue, "d/m/Y");
            $dateReturn .= ' (' .
                $yeea_disp .
                ')';
        } else {
            $dateReturn = $yeea_disp;
        }
    }
    return $dateReturn;
}

## Generate Random Number :
function _genearteRandomNumber($size = 4)
{
    $randomNumber = '';
    $count = 0;
    while ($count < $size) {
        $randomDigit = random_int(0, 9);
        $randomNumber .= $randomDigit;
        $count++;
    }
    $randomNumber = (strlen($randomNumber) <
        6) ? _genearteRandomNumber(6) : $randomNumber;
    return $randomNumber;
}

## Trim Array Remove :
function _trimArrRemove($arr = [])
{
    $arrTemp = array();
    if ($arr != '' && !is_array($arr)) {
        $arr = explode(',', $arr);
    }
    if (isset($arr) && $arr != '' && is_array($arr) && count($arr) > 0) {
        $arr = array_map('trim', $arr);
        foreach ($arr as $arr_val) {
            if ($arr_val != '') {
                $arrTemp[] = $arr_val;
            }
        }
    }
    return $arrTemp;
}

## Trim String Remove :
function _trimStringRemove($arr)
{
    $strTemp = '';
    $arrTemp = _trimArrRemove($arr);
    if (isset($arrTemp) && count($arrTemp) > 0) {
        $strTemp = implode("','", $arrTemp);
        $strTemp = "'" . $strTemp . "'";
    }
    return $strTemp;
}

## Check Is Array :
function _createLabel($key = '')
{
    $label = '';
    if (isset($key) && $key != '') {
        $label = str_replace('_', ' ', $key);
        $label = ucfirst($label);
    }
    return $label;
}

function _getAdminFilterWhereStr($filterData, $table = 'registers')
{
    $returnStr = "";
    if (isset($filterData['gender']) && !blank($filterData['gender'])) {
        $gender = $filterData['gender'];
        if ($gender != 'All') {
            $returnStr .= "( $table.gender = '$gender' )";
        } else {
            $returnStr .= "( $table.gender = 'Male' OR $table.gender = 'Female' )";
        }
    }
    if (isset($filterData['user_type']) && !blank($filterData['user_type'])) {
        $userType = $filterData['user_type'];
        $condition = 'AND';
        if ($userType != 'All') {
            $returnStr .= " $condition ( $table.user_type = '$userType' )";
        } else {
            $returnStr .= " $condition ( $table.user_type = '0' OR $table.user_type = '1' )";
        }
    }
    if (isset($filterData['keyword']) && !blank($filterData['keyword'])) {
        $keyWord = $filterData['keyword'];
        if (isset($keyWord) && $keyWord != '') {
            $condition = 'AND';
            if (blank($returnStr)) {
                $condition = '';
            }
            $returnStr .= " $condition ( $table.fullname like '%$keyWord%' OR $table.matri_id like '%$keyWord%'
            OR $table.gender like '%$keyWord%'
            OR $table.mobile like '%$keyWord%') ";
        }
    }
    if (
        isset($filterData['frm_age']) &&
        !blank($filterData['frm_age']) &&
        isset($filterData['to_age']) &&
        !blank($filterData['to_age'])
    ) {
        $condition = 'AND';
        if (blank($returnStr)) {
            $condition = '';
        }
        $returnStr .= " $condition (( ( date_format( now( ) , '%Y' ) - date_format( birthdate, '%Y' )) -
        ( date_format( now( ) , '00-%m-%d' ) < date_format( birthdate, '00-%m-%d' ) ) ) BETWEEN " .
            $filterData['frm_age'] .
            " AND " .
            $filterData['to_age'] .
            ")";
    }
    if (
        isset($filterData['height']) &&
        !blank($filterData['height']) &&
        isset($filterData['height_to']) &&
        !blank($filterData['height_to'])
    ) {
        $height = $filterData['height'];
        $heightTo = $filterData['height_to'];
        $condition = 'AND';
        if (blank($returnStr)) {
            $condition = '';
        }
        $returnStr .= " $condition (height BETWEEN '" .
            $height .
            "' AND '" .
            $heightTo .
            "' )";
    }
    if (isset($filterData['registered_from']) && !blank($filterData['registered_from'])) {
        $fromDate = (string)$filterData['registered_from'];
        if (strlen($fromDate) === 10) {
            $fromDate .= ' 00:00:00';
        }
        if (!empty($returnStr)) {
            $returnStr .= " AND ";
        }
        $returnStr .= "$table.created_at >= '$fromDate'";
    }
    if (isset($filterData['registered_to']) && !blank($filterData['registered_to'])) {
        $toDate = (string)$filterData['registered_to'];
        if (strlen($toDate) === 10) {
            $toDate .= ' 23:59:59';
        }
        if (!empty($returnStr)) {
            $returnStr .= " AND ";
        }
        $returnStr .= "$table.created_at <= '$toDate'";
    }
    if (isset($filterData['plan_expired_from']) && !blank($filterData['plan_expired_from'])) {
        $fromDate = (string)$filterData['plan_expired_from'];
        if (strlen($fromDate) === 10) {
            $fromDate .= ' 00:00:00';
        }
        if (!empty($returnStr)) {
            $returnStr .= " AND ";
        }
        $returnStr .= "$table.plan_expired_on >= '$fromDate'";
    }
    if (isset($filterData['plan_expired_to']) && !blank($filterData['plan_expired_to'])) {
        $toDate = (string)$filterData['plan_expired_to'];
        if (strlen($toDate) === 10) {
            $toDate .= ' 23:59:59';
        }
        if (!empty($returnStr)) {
            $returnStr .= " AND ";
        }
        $returnStr .= "$table.plan_expired_on <= '$toDate'";
    }
    if (
        isset($filterData['plan_id']) &&
        !blank($filterData['plan_id'])
    ) {
        $planId = $filterData['plan_id'];
        $planId = _trimArrRemove($planId);
        if (isset($planId) && count($planId) > 0) {
            $planIdStr = implode("','", $planId);
            $returnStr .= " AND ( $table.plan_id in ( '$planIdStr') ) ";
        }
    }
    if (isset($filterData['mother_tongue']) && !blank($filterData['mother_tongue'])) {
        $motherTongue = $filterData['mother_tongue'];
        $motherTongue = _trimArrRemove($motherTongue);
        if (isset($motherTongue) && count($motherTongue) > 0) {
            $motherTongueStr = implode("','", $motherTongue);
            $returnStr .= " AND ( $table.mother_tongue in ( '$motherTongueStr') ) ";
        }
    }
    if (
        isset($filterData['marital_status']) &&
        !blank($filterData['marital_status'])
    ) {
        $maritalStatus = $filterData['marital_status'];
        $maritalStatus = _trimArrRemove($maritalStatus);
        if (isset($maritalStatus) && count($maritalStatus) > 0) {
            $maritalStatusStr = implode("','", $maritalStatus);
            $returnStr .= " AND ( $table.marital_status in ( '$maritalStatusStr') ) ";
        }
    }
    if (isset($filterData['religion']) && !blank($filterData['religion'])) {
        $religion = $filterData['religion'];
        $religion = _trimArrRemove($religion);
        if (isset($religion) && count($religion) >  0) {
            $religionStr = implode("','", $religion);
            $returnStr .= " AND ( $table.religion in ( '$religionStr') ) ";
        }
    }
    if (isset($filterData['caste']) && !blank($filterData['caste'])) {
        $caste = $filterData['caste'];
        $caste = _trimArrRemove($caste);
        if (isset($caste) && count($caste) > 0) {
            $casteStr = implode("','", $caste);
            $returnStr .= " AND ( $table.caste in ( '$casteStr') ) ";
        }
    }
    if (isset($filterData['country_id']) && !blank($filterData['country_id'])) {
        $countryId = $filterData['country_id'];
        $countryId = _trimArrRemove($countryId);
        if (isset($countryId) && count($countryId) > 0) {
            $countryIdStr = implode("','", $countryId);
            $returnStr .= " AND ( $table.country_id in ( '$countryIdStr') ) ";
        }
    }
    if (isset($filterData['state_id']) && !blank($filterData['state_id'])) {
        $stateId = $filterData['state_id'];
        $stateId = _trimArrRemove($stateId);
        if (isset($stateId) && count($stateId) > 0) {
            $stateIdStr = implode("','", $stateId);
            $returnStr .= " AND ( $table.state_id in ( '$stateIdStr') ) ";
        }
    }
    if (isset($filterData['city']) && !blank($filterData['city'])) {
        $city = $filterData['city'];
        $city = _trimArrRemove($city);
        if (isset($city) && count($city) > 0) {
            $cityStr = implode("','", $city);
            $returnStr .= " AND ( $table.city in ( '$cityStr') ) ";
        }
    }
    if (isset($filterData['manglik']) && !blank($filterData['manglik'])) {
        $manglik = $filterData['manglik'];
        $manglik = _trimArrRemove($manglik);
        if (isset($manglik) && count($manglik) > 0) {
            $manglikStr = implode("','", $manglik);
            $returnStr .= " AND ( $table.manglik in ( '$manglikStr') ) ";
        }
    }
    if (isset($filterData['income']) && !blank($filterData['income'])) {
        $income = $filterData['income'];
        $income = _trimArrRemove($income);
        if (isset($income) && count($income) > 0) {
            $incomeStr = implode("','", $income);
            $returnStr .= " AND ( $table.income in ( '$incomeStr') ) ";
        }
    }
    if (isset($filterData['education_level']) && !blank($filterData['education_level'])) {
        $educationLevel = $filterData['education_level'];
        $educationLevel = _trimArrRemove($educationLevel);
        if (isset($educationLevel) && count($educationLevel) > 0) {
            $educationWhereStr = [];
            foreach ($educationLevel as $education) {
                $education = trim($education);
                if (!blank($education)) {
                    $educationWhereStr[] = "FIND_IN_SET('$education', $table.education_level)";
                }
            }
            if (!empty($educationWhereStr)) {
                $returnStr .= " AND ( " . implode(' OR ', $educationWhereStr) . " ) ";
            }
        }
    }
    if (isset($filterData['occupation']) && !blank($filterData['occupation'])) {
        $occupation = $filterData['occupation'];
        $occupation = _trimArrRemove($occupation);
        if (isset($occupation) && count($occupation) > 0) {
            $occupationStr = implode("','", $occupation);
            $returnStr .= " AND ( $table.occupation in ( '$occupationStr') ) ";
        }
    }
    if (isset($filterData['diet']) && !blank($filterData['diet'])) {
        $diet = $filterData['diet'];
        $diet = _trimArrRemove($diet);
        if (isset($diet) && count($diet) > 0) {
            $dietStr = implode("','", $diet);
            $returnStr .= " AND ( $table.diet in ( '$dietStr') ) ";
        }
    }
    if (isset($filterData['smoke']) && !blank($filterData['smoke'])) {
        $smoke = $filterData['smoke'];
        $smoke = _trimArrRemove($smoke);
        if (isset($smoke) && count($smoke) > 0) {
            $smokeStr = implode("','", $smoke);
            $returnStr .= " AND ( $table.smoke in ( '$smokeStr') ) ";
        }
    }
    if (isset($filterData['drink']) && !blank($filterData['drink'])) {
        $drink = $filterData['drink'];
        $drink = _trimArrRemove($drink);
        if (isset($drink) && count($drink) > 0) {
            $drinkStr = implode("','", $drink);
            $returnStr .= " AND ( $table.drink in ( '$drinkStr') ) ";
        }
    }
    if (isset($filterData['profileby']) && !blank($filterData['profileby'])) {
        $profileby = $filterData['profileby'];
        $profileby = _trimArrRemove($profileby);
        if (isset($profileby) && count($profileby) > 0) {
            $profilebyStr = implode("','", $profileby);
            $returnStr .= " AND ( $table.profileby in ( '$profilebyStr') ) ";
        }
    }
    if (isset($filterData['family_type']) && !blank($filterData['family_type'])) {
        $familyType = $filterData['family_type'];
        $familyType = _trimArrRemove($familyType);
        if (isset($familyType) && count($familyType) > 0) {
            $familyTypeStr = implode("','", $familyType);
            $returnStr .= " AND ( $table.family_type in ( '$familyTypeStr') ) ";
        }
    }
    if (isset($filterData['family_status']) && !blank($filterData['family_status'])) {
        $familyStatus = $filterData['family_status'];
        $familyStatus = _trimArrRemove($familyStatus);
        if (isset($familyStatus) && count($familyStatus) > 0) {
            $familyStatusStr = implode("','", $familyStatus);
            $returnStr .= " AND ( $table.family_status in ( '$familyStatusStr') ) ";
        }
    }
    if (isset($filterData['plan_status']) && !blank($filterData['plan_status'])) {
        $planStatus = $filterData['plan_status'];
        $planStatus = _trimArrRemove($planStatus);
        if (isset($planStatus) && count($planStatus) > 0) {
            $planStatusStr = implode("','", $planStatus);
            $returnStr .= " AND ( $table.plan_status in ( '$planStatusStr') ) ";
        }
    }
    if (isset($filterData['mobile_verify_status']) && !blank($filterData['mobile_verify_status'])) {
        $mobileVerifyStatus = $filterData['mobile_verify_status'];
        $mobileVerifyStatus = _trimArrRemove($mobileVerifyStatus);
        if (isset($mobileVerifyStatus) && count($mobileVerifyStatus) > 0) {
            $mobileVerifyStatusStr = implode("','", $mobileVerifyStatus);
            $returnStr .= " AND ( $table.mobile_verify_status in ( '$mobileVerifyStatusStr') ) ";
        }
    }
    if (isset($filterData['staff_id']) && !blank($filterData['staff_id'])) {
        $staffId = $filterData['staff_id'];
        $staffId = _trimArrRemove($staffId);
        if (isset($staffId) && count($staffId) > 0) {
            $staffIdStr = implode("','", $staffId);
            $returnStr .= " AND ( $table.staff_assign_id in ( '$staffIdStr') ) ";
        }
    }
    if (isset($filterData['franchise_id']) && !blank($filterData['franchise_id'])) {
        $franchiseId = $filterData['franchise_id'];
        $franchiseId = _trimArrRemove($franchiseId);
        if (isset($franchiseId) && count($franchiseId) > 0) {
            $franchiseIdStr = implode("','", $franchiseId);
            $returnStr .= " AND ( $table.franchise_assign_id in ( '$franchiseIdStr') ) ";
        }
    }
    return $returnStr;
}

## get SiteSetting Data :
function _getSiteSetting($key = null)
{
    $settings = SiteSetting::getSettings();
    if (!$settings) return null;
    return $key ? ($settings[$key] ?? null) : $settings;
}

## Chech Admin Permision :
function _checkPermission($userType, $staffRoleId, $permissionType = '', $return = '')
{
    // Admin shortcut (no DB, no cache)
    if ($userType === 'Admin') {
        return 'Admin';
    }

    // Decide model
    $model = $userType === 'Staff' ? StaffRole::class : FranchiseRole::class;
    $cacheKey = "role_perm_{$userType}_{$staffRoleId}";
    // Cache full role row for 24 hours
    $roleData = Cache::remember(
        $cacheKey,
        now()->addHours(24),
        function () use ($model, $staffRoleId) {
            return $model::select('*')->find($staffRoleId);
        }
    );

    $returnVal = 'No';
    if ($roleData && !empty($permissionType) && !empty($roleData->$permissionType)) {
        $returnVal = $roleData->$permissionType;
    }

    if ($return === 'redirect' && $returnVal === 'No') {
        return redirect()->route('admin.dashboard');
    }

    return $returnVal;
}

## Api Title Change :
function _profileTitle($result)
{
    return $result->matri_id;
}

function _profileSubTitle($member, string $separator = ', '): string
{
    return collect([
        _birthdateDisplay($member->birthdate, 0),
        _displayHeight($member->height),
        optional($member->religionData)->translated_name,
        optional($member->cityData)->translated_name,
        optional($member->stateData)->translated_name,
        optional($member->countryData)->translated_name,
    ])
        ->filter(function ($value) {
            $value = trim((string) $value);
            return $value !== '' && strtoupper($value) !== 'N/A';
        })
        ->implode($separator);
}

function _getMemberLocation($member, string $separator = ', '): string
{
    return collect([
        optional($member->cityData)->translated_name,
        optional($member->stateData)->translated_name,
        optional($member->countryData)->translated_name,
    ])->filter()->implode($separator) ?: 'N/A';
}

function _getMemberAgeHeight($member, string $separator = ', '): string
{
    return collect([
        _birthdateDisplay($member->birthdate, 0),
        _displayHeight($member->height),
        optional($member->religionData)->translated_name,
    ])->filter()->implode($separator) ?: 'N/A';
}

## Check Photo Status :
function _checkPhotoStatus($memberData)
{
    if (!empty($memberData) && $memberData->photo1_status != 'APPROVED') {
        $memberData->photo1 = '';
    }
    return $memberData;
}

## Default Country Code:
function _defaultCountryCode($defaultCountryCode = '+91')
{
    $configArr = _getSiteSetting();
    if (!empty($configArr['default_country_code'])) {
        $defaultCountryCode = $configArr['default_country_code'];
    }
    $cacheKey = 'default_country_code_dropdown';
    return Cache::rememberForever($cacheKey, function () use ($defaultCountryCode) {
        $countryCodes = CountryMaster::active()
            ->whereNotNull('country_code')
            ->orderBy('country_code')
            ->distinct()
            ->pluck('country_code');
        $html = '';
        foreach ($countryCodes as $code) {
            $selected = $code === $defaultCountryCode ? 'selected' : '';
            $html .= '<option ' . $selected . ' value="' . e($code) . '">'
                . e($code) .
                '</option>';
        }
        return $html;
    });
}

function _displayHeight($val = '')
{
    if ($val === '' || $val === null) {
        return 'N/A';
    }

    $val  = intval($val);
    $foot = (int)($val / 12);
    $inch = $val % 12;
    $cm   = (int)ceil($val * 2.54);

    if ($inch > 0) {
        return $foot . "-" . $inch . ' ft';
    }
    return $foot . "-" . $inch . ' ft';
}

function _heightList()
{
    $heights = [];
    for ($i = 48; $i <= 84; $i++) { // 4ft to 7ft in inches
        $heights[$i] = _displayHeight($i);
    }

    return $heights;
}

function _weightList()
{
    $weights = [];
    for ($i = 35; $i <= 150; $i++) {
        $weights[$i] = $i . ' Kg';
    }

    return $weights;
}

function _ageRang()
{
    $ages = [];
    for ($i = 18; $i <= 60; $i++) {
        $ages[$i] = $i . ' Year';
    }

    return $ages;
}

## Get Days Between Two Dates :
function _getDaysBetweenTwoDates($date1, $date2)
{
    $date1 = strtotime($date1); // or your date as well
    $date2 = strtotime($date2);
    $datediff = $date1 - $date2;
    return round($datediff / (60 * 60 * 24));
}

## Add Days In Date :
function _addDayInDate($date, $days)
{
    return date('Y-m-d', strtotime($date . ' + ' . $days . ' days'));
}

## Get Member Placeholder Images :
function _getMemberDefaultImage($gender)
{
    $placeholder = MemberDefaultPlaceholder::getCached();
    if ($gender == 'Male') {
        return _assetUrl('upload_path.PLACEHOLDERS_IMAGE') . $placeholder->male_public_image;
    } else {
        return _assetUrl('upload_path.PLACEHOLDERS_IMAGE') . $placeholder->female_public_image;
    }
}

## Get Member Protected Placeholder Images :
function _getProtectedImage($gender)
{
    $placeholder = MemberDefaultPlaceholder::getCached();
    if ($gender == 'Male') {
        return _assetUrl('upload_path.PLACEHOLDERS_IMAGE') . $placeholder->male_protected_image;
    } else {
        return _assetUrl('upload_path.PLACEHOLDERS_IMAGE') . $placeholder->female_protected_image;
    }
}

function _getMemberProfileImage($memberData, $currentMemberdata = 'No', $photoKey = '')
{
    $photos     = ['photo1', 'photo2', 'photo3', 'photo4'];
    $isCurrent  = strtolower($currentMemberdata) === 'yes';
    $isNotPaid  = ($memberData->plan_status === 'Not Paid');
    // Helper to return valid photo URL
    $getPhotoUrl = function ($key) use ($memberData, $isCurrent, $isNotPaid) {
        if (empty($memberData->$key)) {
            return null;
        }
        // If not paid → try blur photo first
        // if ($isNotPaid) {
        //     if (_checkStorageFileExists('upload_path.MEMBER_BLUR_PHOTOS_URL', $memberData->$key) && ($isCurrent || $memberData->{$key . '_status'} === 'APPROVED')) {
        //         return _assetUrl('upload_path.MEMBER_BLUR_PHOTOS_URL') . $memberData->$key;
        //     }
        // }

        // Normal photo
        // if (_checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $memberData->$key) && ($isCurrent || $memberData->{$key . '_status'} === 'APPROVED')) {
        //     return _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $memberData->$key;
        // }
        if (_checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $memberData->$key) === true && ($isCurrent || $memberData->{$key . '_status'} === 'APPROVED')) {
            return _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $memberData->$key;
        }
        return _getMemberDefaultImage($memberData->gender);
    };

    // If specific photo requested
    if (in_array($photoKey, $photos, true)) {
        $url = $getPhotoUrl($photoKey);
        return $url ?: _getMemberDefaultImage($memberData->gender);
    }

    // Otherwise, return first valid photo
    foreach ($photos as $photo) {
        $url = $getPhotoUrl($photo);
        if ($url) {
            return $url;
        }
    }

    // Default image
    return _getMemberDefaultImage($memberData->gender);
}

## Time Ago :
function _timeAgo($datetime, $full = false)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $weeks = floor($diff->d / 7);
    $days  = $diff->d - ($weeks * 7);

    $string = [
        'y' => $diff->y ? $diff->y . ' year'  : null,
        'm' => $diff->m ? $diff->m . ' month' : null,
        'w' => $weeks   ? $weeks   . ' week'  : null,
        'd' => $days    ? $days    . ' day'   : null,
        'h' => $diff->h ? $diff->h . ' hrs'   : null,
        'i' => $diff->i ? $diff->i . ' min'   : null,
        's' => $diff->s ? $diff->s . ' sec'   : null,
    ];

    $string = array_filter($string);

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }
    return $string ? implode(', ', $string) : 'just now';
}

function _adminUserType($userType)
{
    $retrunArr = 'Admin';
    if (isset($userType) && $userType == 'Staff') {
        $retrunArr = 'Staff';
    } elseif (isset($userType) && $userType == 'Franchise') {
        $retrunArr = 'Franchise';
    }
    return $retrunArr;
}

function _adminRoleId($authUser, $userType)
{
    $roleId = $authUser->id;
    if ($userType == 'Staff') {
        $roleId = $authUser->role_id;
    }
    // Franchise already falls through to $authUser->id, so no elseif needed
    return $roleId;
}

function _getStarRating(float $totalRating = 0): string
{
    $totalRating = max(0, min(5, $totalRating)); // Clamp between 0 and 5

    $fullStars = floor($totalRating);
    $halfStar  = ($totalRating - $fullStars) >= 0.5 ? 1 : 0;
    $emptyStars = 5 - ($fullStars + $halfStar);

    $html = '';

    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="bx bxs-star"></i>';
    }

    // Half star
    if ($halfStar) {
        $html .= '<i class="bx bxs-star-half"></i>';
    }

    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="bx bx-star"></i>';
    }

    return $html;
}

function _convertOembedToIframe($content)
{
    ## Match the oembed URL
    $pattern = '/<oembed url="([^"]*)"><\/oembed>/i';

    ## Convert the YouTube link to an embeddable format
    $content = preg_replace_callback($pattern, function ($matches) {
        $url = $matches[1];

        ## Convert YouTube URL to embeddable iframe format
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $id)) {
            $videoId = $id[1];
            $embedUrl = "https://www.youtube.com/embed/$videoId";
            return '<iframe width="1000" height="400" src="' . $embedUrl . '" frameborder="0" allowfullscreen></iframe>';
        }

        return $matches[0];
    }, $content);
    return $content;
}

## Language For Api:
function _getLangApi(Request $request, $key, $replaceArr = [])
{
    $defaultLanguage = _getConstant('DEFAULT_LANGUAGE');
    $language = $request->header('lang', $defaultLanguage);
    App::setLocale(session('locale', $language));
    return __('messages.' . $key, $replaceArr);
}

## Language For Web:
function _getLang($key, $replaceArr = [])
{
    return __('messages.' . $key, $replaceArr);
}

## Get Current Language :
function _getActiveLanguage()
{
    $cacheKey = 'active_languages';
    return Cache::rememberForever($cacheKey, function () {
        return LanguageMaster::active()
            ->orderBy('id', 'ASC')
            ->select('lang_name', 'lang_code', 'is_default')
            ->limit(10)
            ->get();
    });
}

## Get Default Language :
function _getDefaultLanguage()
{
    return Cache::remember('default_language', 3600, function () {
        $default = LanguageMaster::default()->first();

        return $default?->lang_code ?? _getConstant('DEFAULT_LANGUAGE');
    });
}

## Get Config Constant :
function _getConstant($key)
{
    if (!blank($key)) {
        return config('constants.' . $key);
    }
}

## Get Image url  :
function _assetUrl($key)
{
    if (!blank($key)) {
        $path = config('constants.' . $key);
        $url  = asset('storage/' . $path);
        return str_ends_with($path, '/') ? $url . '/' : $url;

        // return Storage::url(config('constants.' . $key));
    }
}

## Check Storage File Exits or not:
function _checkStorageFileExists($constantPath, $imageValue)
{
    if (Storage::exists(config('constants.' . $constantPath) . $imageValue)) {
        return true;
    } else {
        return false;
    }
}

## Generate Referal Code :
function _createReferalCode($length_of_string)
{
    $str_result = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
    return substr(str_shuffle($str_result), 0, $length_of_string);
}

function _defaultCurrency()
{
    $configArr = _getSiteSetting();
    $defaultCurrency = 'INR';
    if ($configArr['default_currency'] != '') {
        $defaultCurrency = $configArr['default_currency'];
    }
    return $defaultCurrency;
}

function _canViewMemberPhoto($member, bool $hasPhotoRequestAccess): bool
{
    $viewer = auth()->guard('web')->user(); // can be null (guest)

    $photos = ['photo1', 'photo2', 'photo3', 'photo4'];

    // Must have at least one approved photo
    $hasApprovedPhoto = false;
    foreach ($photos as $photo) {
        if (!empty($member->$photo) && $member->{$photo . '_status'} === 'APPROVED') {
            $hasApprovedPhoto = true;
            break;
        }
    }

    if (!$hasApprovedPhoto) {
        return false; // Nothing to show
    }

    $visibility = (int) $member->photo_visibility;

    // 🔹 Case: Show to all (even guests)
    if ($visibility === 1) {
        return true;
    }

    // 🔹 From here, login OR photo request is required
    if ($hasPhotoRequestAccess) {
        return true;
    }

    // 🔹 Guest users cannot see Hidden or Paid photos
    if (!$viewer) {
        return false;
    }

    // 🔹 Paid members only
    if ($visibility === 2) {
        return $viewer->plan_status === 'Paid';
    }

    // 🔹 Hidden (only via request)
    if ($visibility === 0) {
        return false;
    }

    return false;
}

function _checkPhotoExist($memberData)
{
    $photos = ['photo1', 'photo2', 'photo3', 'photo4'];

    ## Check Approved PhotoExist
    $hasApprovedPhoto = false;
    foreach ($photos as $photo) {
        if (!empty($memberData->$photo) && $memberData->{$photo . '_status'} == 'APPROVED') {
            $hasApprovedPhoto = true;
            break;
        }
    }
    return $hasApprovedPhoto;
}

## Get Member Profile Images :
function _getMemberProfileImageApi($memberData, $currentMemberdata = 'No', $photoKey = '')
{
    $photos = ['photo1', 'photo2', 'photo3', 'photo4'];
    $isCurrent = strtolower($currentMemberdata) === 'yes';

    ## Check Approved PhotoExist
    $hasApprovedPhoto = _checkPhotoExist($memberData);

    ## Check Photo Protect
    if (!$isCurrent && $hasApprovedPhoto && (isset($memberData->is_photoProtected) || isset($memberData->photo_visibility))) {
        if (!isset($memberData->is_photoProtected)) {
            $isPhotoProtected = _checkPhotoProtectedApi(0, $memberData->photo_visibility ?? 0);
        } else {
            $isPhotoProtected = $memberData->is_photoProtected;
        }
        if ($isPhotoProtected == 0) {
            return _getProtectedImage($memberData->gender);
        }
    }

    ## For Get Single Photo
    if (in_array($photoKey, $photos)) {
        if (!empty($memberData->$photoKey) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $memberData->$photoKey)) {
            if ($isCurrent || $memberData->{$photoKey . '_status'} == 'APPROVED') {
                return _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $memberData->$photoKey;
            }
        }
        ## Default Images :
        return '';
    }

    ## For All Photos :
    foreach ($photos as $photo) {
        if (!empty($memberData->$photo) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $memberData->$photo)) {
            if ($isCurrent || $memberData->{$photo . '_status'} == 'APPROVED') {
                return _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $memberData->$photo;
            }
        }
    }
    ## Default Images :
    return '';
}

function _checkPhotoProtectedApi($isPhotoProtected, $photoVisibility)
{
    $currentMemberData = auth()->guard()->user();
    if ($photoVisibility == 1 || $isPhotoProtected) {
        return 1; // Show to all or explicitly protected
    }
    if ($photoVisibility == 0 || $isPhotoProtected) {
        return 0; // Hide for all
    }
    if ($photoVisibility == 2 && Auth::check() && $currentMemberData->plan_status != 'Paid') {
        return 0; // Show to not a paid members
    }
    if ($photoVisibility == 2 && Auth::check() && $currentMemberData->plan_status == 'Paid') {
        return 1; // Show to paid members
    }
    return 0;
}

function _encrypt($id)
{
    return urlencode(Crypt::encryptString((string)$id));
}

function _decrypt($encId)
{
    try {
        return Crypt::decryptString(urldecode($encId));
    } catch (DecryptException $e) {
        return null;
    }
}

function _getAdminUnreadAlertCount(array|string|null $columns = []): bool
{
    $adminId = Auth::id();

    if (!$adminId) {
        return false;
    }

    $allowedColumns = _getStaticArr('adminAlertType');

    // Accept "photo_upload" or ["photo_upload", "selfie_upload"]
    $requested = array_filter(Arr::wrap($columns));

    if ($requested) {
        $checkColumns = array_values(array_intersect($requested, $allowedColumns));

        // Columns were requested but none are valid: don't fall back to "any alert"
        if (!$checkColumns) {
            return false;
        }
    } else {
        // Nothing requested means "any alert column"
        $checkColumns = $allowedColumns;
    }

    sort($checkColumns);
    $version  = Cache::get("admin_alerts_v_{$adminId}", 0);
    $cacheKey = "admin_unread_alerts_{$adminId}_v{$version}_" . hash('sha256', implode(',', $checkColumns));

    return Cache::remember($cacheKey, 60, function () use ($adminId, $checkColumns) {
        return AdminAlert::where('admin_id', $adminId)
            ->where('admin_type', AdminAlert::TYPE_ADMIN)
            ->where(function ($q) use ($checkColumns): void {
                foreach ($checkColumns as $column) {
                    $q->orWhere($column, AdminAlert::STATUS_UNREAD);
                }
            })
            ->exists();
    });
}

function _getLangNamesFromIds($modelClass, $idsString, $nameColumn, $langCode = null)
{
    if (blank($idsString)) {
        return null;
    }

    $ids = array_filter(explode(',', $idsString));
    $defaultLang = _getDefaultLanguage();
    $langCode = $langCode ?: app()->getLocale();

    $cacheKey = "names_from_ids_{$modelClass}_{$langCode}_" . hash('sha256', $idsString);

    return Cache::rememberForever($cacheKey, function () use ($modelClass, $ids, $nameColumn, $langCode, $defaultLang) {

        // Step 1: Get base records (default language)
        $base = $modelClass::active()
            ->where('lang_code', $defaultLang)
            ->whereIn('id', $ids)
            ->select('id', $nameColumn)
            ->get()
            ->keyBy('id');

        if ($base->isEmpty()) {
            return null;
        }

        // Step 2: If same language, return directly
        if ($langCode === $defaultLang) {
            return $base->pluck($nameColumn)->implode(', ');
        }

        // Step 3: Get translations mapped by lang_id (base id)
        $translations = $modelClass::active()
            ->where('lang_code', $langCode)
            ->whereIn('lang_id', $ids)
            ->pluck($nameColumn, 'lang_id');

        // Step 4: Merge translated names
        $names = [];
        foreach ($base as $id => $row) {
            $names[] = $translations[$id] ?? $row->{$nameColumn};
        }

        return implode(', ', $names);
    });
}

## Member Online Status :
function _memberOnlineStatus($member): array
{
    $lastActivity = $member->last_activity;

    if (blank($lastActivity)) {
        return [
            'status_code'  => 'offline',
            'status_text'  => __('messages.lbl_offline'),
            'status_class' => 'text-danger',
        ];
    }

    $last = Carbon::parse($lastActivity);

    // SIGNED difference (very important)
    $diffMinutes = (int) floor($last->diffInMinutes(now(), false));

    $onlineMinutes = (int) config('constants.MEMBER_LAST_ACTIVITY_DURATION'); // 5

    // If last_activity is in future → force offline (timezone bug protection)
    if ($diffMinutes < 0) {
        return [
            'status_code'  => 'offline',
            'status_text'  => __('messages.lbl_offline'),
            'status_class' => 'text-danger',
        ];
    }

    // Online
    if ($diffMinutes <= $onlineMinutes) {
        return [
            'status_code'  => 'online',
            'status_text'  => __('messages.lbl_online'),
            'status_class' => 'text-success',
        ];
    }

    // Recently active in minutes
    if ($diffMinutes < 60) {
        return [
            'status_code'  => 'recent',
            'status_text'  => __('messages.lbl_online_min_ago', [
                'minutes' => $diffMinutes
            ]),
            'status_class' => 'text-warning',
        ];
    }

    // Recently active in hours
    if ($diffMinutes < 1440) {
        $hours = intdiv($diffMinutes, 60);

        return [
            'status_code'  => 'recent',
            'status_text'  => __('messages.lbl_online_hour_ago', [
                'hours' => $hours
            ]),
            'status_class' => 'text-warning',
        ];
    }

    // Last seen date
    return [
        'status_code'  => 'offline',
        'status_text'  => __('messages.lbl_active_on_time', [
            'date' => $last->format('d M y'),
        ]),
        'status_class' => 'text-danger',
    ];
}

function _navActive($routes = [], $class = 'active')
{
    foreach ((array) $routes as $route) {
        if (request()->routeIs($route) || request()->is($route)) {
            return $class;
        }
    }
    return '';
}

function _navActiveParam($route, $paramKey, $paramValue, $class = 'active')
{
    if (request()->routeIs($route) && request()->route($paramKey) == $paramValue) {
        return $class;
    }
    return '';
}

function _checkFieldEnable(string $field, string $page): bool
{
    return MemberFieldCheck::isFieldEnabled($field, $page);
}

function _checkAdminAccessOnly($accessType = '')
{
    if (!Auth::check()) {
        return redirect()->route('admin.login');
    }

    if ($accessType != '') {
        return null;
    }

    $userType = _adminUserType(Auth::user()->type);
    if (in_array($userType, ['Staff', 'Franchise'])) {
        return redirect()->route('admin.dashboard');
    }
    return null;
}

function _getDataNames(string $model, ?string $ids, string $column): ?string
{
    if (blank($ids)) {
        return null;
    }

    $ids = collect(explode(',', $ids))
        ->map(fn($id) => (int) trim($id))
        ->filter()
        ->unique()
        ->values();

    if ($ids->isEmpty()) {
        return null;
    }

    return $model::whereIn('id', $ids)
        ->pluck($column)
        ->implode(', ');
}

function _getCurrentTo15date()
{
    $fromDate = Carbon::now()->subDays(15)->startOfDay();
    $toDate = Carbon::now();
    // Prepare the array
    return [
        'call_from' => $fromDate,
        'call_to' => $toDate
    ];
}

function _calculateTimeDifference($startTime, $endTime)
{
    $start = Carbon::parse($startTime);
    $end = Carbon::parse($endTime);
    $returnStr = $start->diff($end)->format('%h hours, %i minutes, %s seconds');
    return $returnStr;
}

function _checkImageUrl(string $path, ?string $filename, ?string $gender = null): string
{
    if (empty($filename) || !_checkStorageFileExists($path, $filename)) {
        if (!blank($gender)) {
            return _getMemberDefaultImage($gender);
        } else {
            return '';
        }
    }

    return _assetUrl($path) . $filename;
}

function _checkImageUrlApi(string $path, ?string $filename, ?string $gender = null): string
{
    if (empty($filename) || !_checkStorageFileExists($path, $filename)) {
        // if (!blank($gender)) {
        //     return _getMemberDefaultImage($gender);
        // } else {
        return '';
        // }
    }

    return _assetUrl($path) . $filename;
}

if (! function_exists('_generateOtp')) {
    function _generateOtp(int $length = 6): string
    {
        // Fixed OTP only when the demo flag is on AND the app is not in production
        if (_getConstant('DISABLE_DEMO') === 'Enabled' && ! app()->isProduction()) {
            return _getConstant('DEMO_CREDENTIALS.demo_otp');
        }
        return (string) random_int(10 ** ($length - 1), (10 ** $length) - 1);
    }
}

function _yearFormat($key = null, $forDisplay = false){
    $year = array('0 Year', 'Less than 1' => 'Less than 1 Year');
    for ($i = 1; $i <= 40; $i++) {
        $year[] = $i . ' Years';
    }
    if (!blank($key) && isset($year[$key])) {
        return $year[$key];
    }
    if ($forDisplay == false) {
        return $year;
    }
    return '';
}
