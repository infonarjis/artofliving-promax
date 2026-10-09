<?php

namespace App\Services;

use App\Models\CountryMaster;
use App\Models\RegisterPartner;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminFormBuilderService
{
    public $otherData;

    public function generateFormElement($elementArr = [], $otherData = [])
    {
        $this->otherData = $otherData;

        $htmlStrReturn = "";
        if (!empty($elementArr)) {
            ## Get Raw Data :
            if (!empty($this->otherData) && isset($this->otherData['mode']) && $this->otherData['mode'] == 'edit' && isset($this->otherData['id']) && $this->otherData['id'] != '') {
                $rowData = $this->otherData['rowData'];
                // $rowData = $rowData ? $rowData->toArray() : [];
                $rowData = $this->otherData['rowData'] ?? [];

                if (is_object($rowData) && method_exists($rowData, 'toArray')) {
                    $rowData = $rowData->toArray();
                } elseif (is_object($rowData)) {
                    $rowData = get_object_vars($rowData);
                }
                if (empty($rowData)) {
                    if ($this->otherData['mode'] == 'edit' && empty($this->otherData['rowData'])) {
                        return redirect()->route($this->otherData['callbackUrl'])->with('error', 'Data not found.');
                    }
                    $tableName = $this->otherData['tableName'];
                    $id = $this->otherData['id'];
                    if ($tableName == 'register_partners') {
                        $rowData = RegisterPartner::where('member_id', $id)->first();
                    } elseif ($tableName == 'site_config') {
                        $cacheKey = "site_config_admin_{$id}";
                        $rowData = Cache::rememberForever($cacheKey, function () use ($id) {
                            return SiteSetting::find($id)?->toArray();
                        });
                    } else {
                        $rowData = DB::table($tableName)->find($id);
                    }
                }
                $this->otherData['rowData'] = (array)$rowData;
            }
            foreach ($elementArr as $key => $value) {
                if (isset($this->otherData['rowData'][$key])) {
                    $value['value'] = $this->otherData['rowData'][$key];
                }
                if (isset($value['type']) && $value['type'] == 'textbox') {
                    $htmlStrReturn .= $this->generateTextbox($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'password') {
                    $htmlStrReturn .= $this->generatePassword($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'textarea') {
                    $htmlStrReturn .= $this->generateTextarea($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'file') {
                    $htmlStrReturn .= $this->generateFileUpload($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'radio') {
                    $htmlStrReturn .= $this->generateRadio($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'checkbox') {
                    $htmlStrReturn .= $this->generateCheckbox($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'dropdown') {
                    $htmlStrReturn .= $this->generateDropdown($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'mobile') {
                    $htmlStrReturn .= $this->generateMobile($value, $key);
                } elseif (isset($value['type']) && $value['type'] == 'manual') {
                    $htmlStrReturn .= $value['code'];
                } else {
                    $htmlStrReturn .= $this->generateTextbox($value, $key);
                }
            }
        }
        if (!blank($htmlStrReturn)) {
            ## Set Call Back URL :
            if (isset($this->otherData['callbackUrl']) && !empty($this->otherData['callbackUrl'] != '')) {
                $callbackUrl = $this->otherData['callbackUrl'];
                $htmlStrReturn .= '<input type="hidden" name="callbackUrl" id="callbackUrl" value="' . $callbackUrl . '">';
            }
            ## Set Hidden Id :
            if (!isset($this->otherData['isDynamic']) || $this->otherData['isDynamic'] == 'No') {
                if (isset($this->otherData['mode']) && $this->otherData['mode'] == 'edit' && isset($this->otherData['id']) && $this->otherData['id'] != '') {
                    $id = $this->otherData['id'];
                    $htmlStrReturn .= '<input type="hidden" name="id" id="id" value="' . $id . '">';
                    $htmlStrReturn .= '<input type="hidden" name="mode" id="mode" value="edit">';
                } else {
                    $htmlStrReturn .= '<input type="hidden" name="mode" id="mode" value="add">';
                }
            }
        }
        return $htmlStrReturn;
    }

    ## Generate Text Box :
    public function generateTextbox($elementData = [], $name = "")
    {
        $returnContent = '';
        if (!empty($elementData) && $name != '') {
            $currentValue = $this->getValue($elementData, 'value', '');
            $currentValue = html_entity_decode($currentValue);

            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $inputType = $this->getValue($elementData, 'input_type', 'text');
            $other = $this->getValue($elementData, 'other');
            $class = $this->getValue($elementData, 'class');
            $columnClass = $this->getValue($elementData, 'column', '');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $placeHolder = $this->getValue($elementData, 'placeholder', $label);
            $allowedOnlyNumChar = $this->getValue($elementData, 'type_num_alph', '');
            $alpNumStrFun = '';
            if ($allowedOnlyNumChar == 'alpha') {
                $alpNumStrFun = 'onkeypress="return onlyAlphabets(event,this);"';
            } elseif ($allowedOnlyNumChar == 'capitalize') {
                $alpNumStrFun = 'onkeypress="allowOnlyLetters(event)" oninput="validateNameInput(this)"';
            } elseif ($allowedOnlyNumChar == 'num') {
                $alpNumStrFun = 'onkeypress="return isNumberKey(event)"';
            } elseif ($allowedOnlyNumChar == 'tel') {
                $alpNumStrFun = 'oninput="validateAlternateNumber(this)"';
            }
            $isMultiple = $this->getValue($elementData, 'is_multiple');
            $isMultiPar = '';
            if ($isMultiple == 'yes') {
                $isMultiPar = '[]';
            }

            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }

            ## Changes For Member Add Edit :
            $isFieldRegister = $this->getValue($elementData, 'is_register');
            $registerStart = '';
            $registerend = '';
            $regRequired = '';
            if ($isRequired) {
                $regRequired = '<span class="Form__Error">*</span>';
            }
            if ($isFieldRegister != '' && $isFieldRegister == 'yes') {
                $formGroupClassRegister = $this->getValue($elementData, 'form_register_class');
                $registerStart = '<div class="col-lg-6 col-md-6 ' . $formGroupClassRegister . '"><div class="edit_inputMain-sltr">';
                $registerend = '</div></div>';
            }
            $modeType = $this->getValue($elementData, 'modeType', '');
            $checkDisableDemoField = $this->getValue($elementData, 'isDisableInDemo', 'No');
            ## Maxlength
            $maxLength = $this->getValue($elementData, 'maxLength', '');
            $maxLengthStr = (!blank($maxLength) ? 'maxlength="' . $maxLength . '"' : '');

            ## Minlength
            $minLength = $this->getValue($elementData, 'minLength', '');
            $minLengthStr = (!blank($minLength) ? 'minlength="' . $minLength . '"' : '');

            if ($inputType == 'date') {
                $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                    <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>
                    <input class="form-control ' . $class . '" type="' .
                    $inputType . '" value="' . htmlentities(stripcslashes($currentValue)) . '" ' .
                    $other . '  id="' . $name . '" name="' . $name . '">
                    </div>' . $registerend;
            } elseif ($inputType == 'datetime-local') {
                $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>
                <input class="form-control ' . $class . '" type="' .
                    $inputType . '" value="' . htmlentities(stripcslashes($currentValue)) . '" ' .
                    $other . '  id="' . $name . '" name="' . $name . '">
                </div>' . $registerend;
            } elseif ($inputType == 'time') {
                $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                    <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>
                    <input class="form-control ' . $class . '" type="' .
                    $inputType . '" value="' . htmlentities(stripcslashes($currentValue)) .
                    '" id="' . $name . '" name="' . $name . '">
                                    </div>' . $registerend;
            } elseif ($inputType == 'hidden') {
                $returnContent .= '<input type="hidden" name="' . $name . '" id="' . $name .
                    '" value="' . htmlentities(stripcslashes($currentValue)) . '" />';
            } else {
                if (_getConstant('DISABLE_DEMO') == 'Enabled' && $modeType == 'edit' && $checkDisableDemoField == 'Yes') {
                    $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                        <label class="form-label" for="' . $name . '">' . $label . $infoTooltip .
                        '</label> <h6>' . _getConstant('DISABLE_IN_DEMO_LABEL') . '</h6>
                        </div>' . $registerend;
                } else {
                    $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                        <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>
                        <input  ' . $other . ' ' . $alpNumStrFun . 'type="' .
                        $inputType . '" ' . $isRequired . ' class="form-control ' . $class . '" id="' .
                        $name . '" name="' . $name . $isMultiPar . '" placeholder="' . $placeHolder . '" value="' .
                        htmlentities(stripcslashes($currentValue)) . '" ' . $maxLengthStr . ' ' . $minLengthStr . ' />
                    </div>' . $registerend;
                }
            }
        }
        return $returnContent;
    }

    ## Generate Password :
    public function generatePassword($elementData = [], $name = "")
    {
        $returnContent = '';
        if (count($elementData) > 0 && $name != '') {
            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $other = $this->getLabel($elementData, 'other');
            $class = $this->getLabel($elementData, 'class');
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $placeHolder = $this->getValue($elementData, 'placeholder', $label);
            $mode = $this->getValue($elementData, 'mode');
            $columnClass = $this->getValue($elementData, 'column', '');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }

            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }
            ## Member Add Edit:
            $isFieldRegister = $this->getValue($elementData, 'is_register');
            $registerStart = '';
            $registerend = '';
            if ($isFieldRegister != '' && $isFieldRegister == 'yes') {
                $registerStart = '<div class="col-lg-6 col-md-6"><div class="edit_inputMain-sltr">';
                $registerend = '</div></div>';
            }
            $regRequired = '';
            if ($isRequired == ' required ') {
                $regRequired = '<span class="Form__Error">*</span>';
            }
            if ($mode == 'edit') {
                $isRequired = '';
                $regRequired = '';
            }
            $returnContent .= $registerStart . '
                <div class="mb-3 ' . $columnClass . $formGroupClass . '">
                    <label class="form-label" for="' . $name . '">
                        ' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '
                    </label>
                    <div class="input-group">
                        <input ' . $other . ' type="password" ' . $isRequired . ' class="form-control ' . $class . ' password-field" id="' . $name . '" name="' . $name . '" autocomplete="new-' . $name . '" placeholder="' . $placeHolder . '" />
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="' . $name . '"> <i class="bx bx-hide"></i></button>
                    </div>
                </div>
                ' . $registerend;
        }
        return $returnContent;
    }

    ## Generate Textarea :
    public function generateTextarea($elementData = [], $name = "")
    {
        $returnContent = '';
        if (count($elementData) > 0 && $name != '') {
            $currentValue = $this->getValue($elementData, 'value', '');
            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $other = $this->getValue($elementData, 'other');
            $class = $this->getValue($elementData, 'class');
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $placeHolder = $this->getValue($elementData, 'placeholder', $label);
            $minlength = $this->getLabel($elementData, 'minlength');
            $maxlength = $this->getValue($elementData, 'maxlength');
            $columnClass = $this->getValue($elementData, 'column', '');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            $buttonDisplay = $this->getValue($elementData, 'button_display', '');
            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }
            ## Member Add Edit:
            $isFieldRegister = $this->getValue($elementData, 'is_register');
            $registerStart = '';
            $registerend = '';
            $regRequired = '';
            if ($isRequired == ' required ') {
                $regRequired = '<span class="Form__Error">*</span>';
            }
            if ($isFieldRegister != '' && $isFieldRegister == 'yes') {
                $registerStart = '<div class="col-lg-12"><div class="edit_inputMain-sltr">';
                $registerend = '</div></div>';
            }

            $checkTextLimit = '';
            $maxlengthHtml = '';
            if ($maxlength > 0) {
                $checkTextLimit = '<small class="maxCharact mt-1">Character Limit : <span id="' . $name . '_count">
                    ' . strlen(old('name', $currentValue ?? '')) . '</span>/500</small>';
                $class .= ' limit-char ';
                $maxlengthHtml = ' maxlength="' . $maxlength . '"';
            }

            ## Display Notes :
            $displayNote = $this->getValue($elementData, 'display_note', '');
            $displayNoteValue = '';
            if ($displayNote != '') {
                $displayNoteValue = '<p class="display-note-field">' . $displayNote . '</p>';
            }

            $modeType = $this->getValue($elementData, 'modeType', '');
            $checkDisableDemoField = $this->getValue($elementData, 'isDisableInDemo', 'No');
            if (_getConstant('DISABLE_DEMO') == 'Enabled' && $modeType == 'edit' && $checkDisableDemoField == 'Yes') {
                $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                    <label class="form-label" for="' . $name . '">' . $label . $infoTooltip .
                    '</label> <h6>' . _getConstant('DISABLE_IN_DEMO_LABEL') . '</h6>
                </div>' . $registerend;
            } else {

                $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                    <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . ' ' . $buttonDisplay . '</label>
                    <textarea ' . $other . ' minlength="' . $minlength . '" ' . $maxlengthHtml .
                    'data-target="' . $name . '_count"' .
                    $isRequired . ' id="' . $name . '" name="' . $name . '" class="form-control ' .
                    $class . '" placeholder="' . $placeHolder . '">' . $currentValue . '</textarea>
                                    ' . $checkTextLimit . $displayNoteValue . '</div>' . $registerend;
            }
        }
        return $returnContent;
    }

    ## Generate File Upload :
    public function generateFileUpload($elementData = [], $name = "")
    {
        $returnContent = '';
        if (count($elementData) > 0 && $name != '') {
            $currentValue = $this->getValue($elementData, 'value', '');
            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $other = $this->getValue($elementData, 'other');
            $class = $this->getValue($elementData, 'class');
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $placeHolder = $this->getValue($elementData, 'placeholder', $label);
            $displayNote = $this->getValue($elementData, 'display_note', '');
            $extension = $this->getValue($elementData, 'extension', 'jpg,png,jpeg,gif,bmp');
            $pathValue = $this->getValue($elementData, 'path_value', '');
            $displayImg = $this->getValue($elementData, 'display_img', 'Yes');
            $columnClass = $this->getValue($elementData, 'column', '');
            $maxFileUpload = $this->getValue($elementData, 'max_file_upload', '10');
            $cropImageClass = $this->getValue($elementData, 'crop_image', '');
            $fileType = $this->getValue($elementData, 'file_type', '');
            if (!blank($cropImageClass) && $cropImageClass == 'Yes') {
                $cropImageClass = 'crop_image';
            }
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }

            ## Member Add Edit:
            $isFieldRegister = $this->getValue($elementData, 'is_register');
            $registerStart = '';
            $registerend = '';
            $regRequired = '';
            if ($isRequired) {
                $regRequired = '<span class="Form__Error">*</span>';
            }
            if ($currentValue != '') {
                $isRequired = '';
                $class = '';
            }
            if ($isFieldRegister != '' && $isFieldRegister == 'yes') {
                $registerStart = '<div class="col-lg-6 col-md-6"><div class="edit_inputMain-sltr">';
                $registerend = '</div></div>';
            } else {
                $registerStart = '<div class="col-lg-6 col-md-6">';
                $registerend = '</div>';
            }

            ## Display Image:
            $imgDisplay = '';
            if (!blank($currentValue) && _checkStorageFileExists($pathValue, $currentValue) && $displayImg == 'Yes') {
                $displayFilePath = _assetUrl($pathValue) . $currentValue;

                if (isset($inline_style) && $inline_style != '') {
                    $imgDisplay = '
                    <div class="text-center">
                        <div class="mb-2">
                            <img src="' . $displayFilePath . '" alt="' . $name . '" class="img-fluid ' . $name . '" style="' . $inline_style . '">
                        </div>
                        <a href="' . $displayFilePath . '" target="_blank" class="btn btn-sm btn-primary">View File</a>
                    </div>';
                } elseif($fileType == 'video') {
                    $imgDisplay = '
                    <div class="text-center">
                        <a href="' . $displayFilePath . '" target="_blank" class="btn btn-sm btn-primary">View File</a>
                    </div>';
                } else {
                    $imgDisplay = '
                    <div class="text-center">
                        <div class="mb-2">
                            <img src="' . $displayFilePath . '" alt="' . $name . '" class="img-fluid ' . $name . '" style="max-height:100px; max-width:100%;">
                        </div>
                        <a href="' . $displayFilePath . '" target="_blank" class="btn btn-sm btn-primary">View File</a>
                    </div>';
                }
            }

            if ($displayNote != '') {
                $displayNote = '<p class=help-block><b>Note:</b> ' . $displayNote . '.</p>';
            }
            $returnContent .= '<div class="col-md-12"><div class="row">' . $registerStart . '<div class="mb-3 ' . $columnClass . $formGroupClass . '">
                <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>
                <input ' . $other . ' ' . $isRequired . ' name="' . $name . '" class="form-control ' . $cropImageClass . ' ' . $class . '" placeholder="' . $placeHolder . '"  type="file" id="' . $name . '" />
                <input type="hidden" name="' . $name . '_val" id="' . $name . '_val" value="' . $currentValue . '" />
                <input type="hidden" name="' . $name . '_path" id="' . $name . '_path" value="' . $pathValue . '" />
                <input type="hidden" name="' . $name . '_ext" id="' . $name . '_ext" value="' . $extension . '" />
                <div class="form-text">Allowed Maximum File size up to '.$maxFileUpload.'. File type ' .
                str_replace(',', ' , ', $extension) . '.</div>' . $displayNote . '</div>' . $registerend . '
                <div class="col-md-6 mb-3 ' . $formGroupClass . '">
                    ' . $imgDisplay . '
                </div></div></div>';
        }
        return $returnContent;
    }

    ## Generate Radio :
    public function generateRadio($elementData = [], $name = "")
    {
        $returnContent = '';
        if (count($elementData) > 0 && $name != '') {
            $currentValue = $this->getValue($elementData, 'value', '');
            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $class = $this->getValue($elementData, 'class');
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $valueArr = $this->getValue($elementData, 'value_arr');
            $onclick = $this->getValue($elementData, 'onclick');
            $extra = $this->getValue($elementData, 'extra', '');
            if ($onclick != '') {
                $onclick = ' onclick= "' . $onclick . '" ';
            }
            $classConVal = $this->getValue($elementData, 'class_con_val');
            $columnClass = $this->getValue($elementData, 'column', '6');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            $isMultiple = $this->getValue($elementData, 'is_multiple');
            $rowIndex = $this->getValue($elementData, 'row_index', '');
            if ($isMultiple == 'yes') {
                $name .= '[' . ($rowIndex - 1) . ']';
            }

            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }

            ## Member Add Edit:
            $isFieldRegister = $this->getValue($elementData, 'is_register');
            $registerStart = '';
            $registerend = '';
            $registerFormLae = '';
            $registerForm = '';
            $registerFormEnd = '';
            $regRequired = $this->isButtonRequired($isRequired);

            $registerStart = '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '"><div class="edit_inputMain-sltr">';
            $registerFormLae = '<div class="radio_mainDivGroups">';
            $registerForm = '<div class="form-check d-inline-block me-3 mt-2">';
            $registerFormEnd = '</div>';
            $registerend = '</div></div></div>';

            $modeType = $this->getValue($elementData, 'modeType', '');
            $checkDisableDemoField = $this->getValue($elementData, 'isDisableInDemo', 'No');
            if (_getConstant('DISABLE_DEMO') == 'Enabled' && $modeType == 'edit' && $checkDisableDemoField == 'Yes') {
                $returnContent .= $registerStart . '<label class="form-label">' . $label . ' ' . $infoTooltip . ' ' . $regRequired .
                    '&nbsp;&nbsp;</label><h6>' . _getConstant('DISABLE_IN_DEMO_LABEL') . '</h6>' . $registerFormLae;
                $returnContent .= $registerend;
            } else {
                $returnContent .= $registerStart . '<label class="form-label">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>' . $registerFormLae;
                foreach ($valueArr as $key_r => $value_arr_val) {
                    $selectedRadio = '';
                    if ($currentValue == $key_r) {
                        $selectedRadio = 'checked';
                    }
                    $radioId = str_replace(['[', ']'], '_', $name) . '_' . $key_r;
                    $classNew = $class;
                    if ($classConVal != '') {
                        $classNew .= ' ' . $classConVal . '_' . $key_r;
                    }
                    $returnContent .= $registerForm . '<input ' . $isRequired . ' ' . $extra . ' ' .
                        $onclick . ' ' . $selectedRadio . ' name="' . $name . '" id="' . $radioId .
                        '" class="form-check-input ' . $classNew . '" type="radio" value="' . $key_r . '" />
                                    <label class="form-label" for="' . $radioId . '">' . $value_arr_val .
                        '</label>' . $registerFormEnd;
                }
                $returnContent .= $registerend;
            }
        }
        return $returnContent;
    }

    public function isButtonRequired($isRequired)
    {
        $regRequired = '';
        if ($isRequired) {
            $regRequired = '<span class="Form__Error">*</span>';
        }
        return $regRequired;
    }

    ## Generate Checkbox :
    public function generateCheckbox($elementData = [], $name = "")
    {
        $returnContent = '';
        if (count($elementData) > 0 && $name != '') {
            $currentValue = $this->getValue($elementData, 'value', 'age');
            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $isMultiple = $this->getValue($elementData, 'is_multiple');
            $class = $this->getValue($elementData, 'class');
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $valueArr = $this->getValue($elementData, 'value_arr');
            $columnClass = $this->getValue($elementData, 'column', '');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }

            $onclick = $this->getValue($elementData, 'onclick');
            $extra = $this->getValue($elementData, 'extra', '');
            if ($onclick != '') {
                $onclick = ' onclick= "' . $onclick . '" ';
            }
            $isMultiPar = '';
            $currentSelectedArra = [];
            if ($isMultiple != '' && $isMultiple == 'yes') {
                $isMultiPar = '[]';
                if ($currentValue != '') {
                    $currentSelectedArra = explode(',', $currentValue);
                }
            } elseif ($currentValue != '') {
                $currentSelectedArra[] = $currentValue;
            }
            $currentSelectedArra = array_map('trim', $currentSelectedArra);

            $returnContent .= '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                <label class="form-label">' . $label . ' ' . $infoTooltip . '</label>
                <div class="radio_mainDivGroups">';

            $hiddenPrinted = false;

            foreach ($valueArr as $key_r => $value_arr_val) {

                $selectedChechbox = in_array($key_r, $currentSelectedArra) ? 'checked' : '';

                $enableFieldInput = '';

                // print hidden only once
                if ($formGroupClass == 'field-enable-div' && !$hiddenPrinted) {
                    $hiddenPrinted = true;
                    // remove [] safely
                    $hiddenName = str_replace('[]', '', $name . $isMultiPar);

                    $enableFieldInput = '<input type="hidden" name="' . $hiddenName . '" value="">';
                }

                $id = $name . '_' . $key_r;
                $returnContent .= '
                    <div class="form-check d-inline-block me-3 mt-2">
                        ' . $enableFieldInput . '
                        <input ' . $isRequired . ' ' . $extra . ' ' . $onclick . ' ' . $selectedChechbox . '
                            name="' . $name . $isMultiPar . '"
                            id="' . $id . '"
                            class="form-check-input ' . $class . '"
                            type="checkbox"
                            value="' . $key_r . '" />

                        <label class="form-check-label" for="' . $id . '">' . $value_arr_val . '</label>
                    </div>';
            }

            $returnContent .= '</div></div>';
        }
        return $returnContent;
    }

    ## Generate Dropdown :
    public function generateDropdown($elementData = [], $name = '')
    {
        $returnContent = '';
        if (count($elementData) > 0 && $name != '') {
            $currentValue = $this->getValue($elementData, 'value', 'age');
            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $isMultiple = $this->getValue($elementData, 'is_multiple');
            $is_multiReg = $this->getValue($elementData, 'is_multi');
            $isFieldRegister = $this->getValue($elementData, 'is_register');
            $class = $this->getValue($elementData, 'class');
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $valueArr = $this->getValue($elementData, 'value_arr');
            $onchange = $this->getValue($elementData, 'onchange');
            $columnClass = $this->getValue($elementData, 'column', '');
            $show_top_country_base = $this->getValue($elementData, 'show_top_country_base', '');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }

            $extra = $this->getValue($elementData, 'extra', '');
            $displayPlaceholder = $this->getValue($elementData, 'display_placeholder', 'Yes');
            if ($displayPlaceholder == 'No') {
                $extra .= ' data-placeholder="Select ' . $label . '"';
            }

            if ($onchange != '') {
                $onchange = ' onchange= "' . $onchange . '" ';
            }
            $isMultiPar = '';
            $isMulti = '';
            $multipleHiddenField = '';
            if ($isMultiple != '' && $isMultiple == 'yes') {
                $isMulti = 'multiple';
                $isMultiPar = '[]';
                if ($currentValue != '') {
                    if (str_starts_with($currentValue, '[')) {
                        $currentSelectedArra = json_decode($currentValue, true) ?? [];
                    } else {
                        $currentSelectedArra = explode(',', $currentValue);
                    }
                }
                $multipleHiddenField = '<input type="hidden" name="' . $name . '" value="">';
            } elseif ($currentValue != '') {
                $currentSelectedArra[] = $currentValue;
            }
            if ($is_multiReg == 'yes') {
                $isMultiPar = '[]';
            }
            $currentSelectedArra = array_map('trim', $currentSelectedArra);
            if (!isset($valueArr) || $valueArr == '' || count($valueArr) == 0) {
                $valueArr = $this->getRelationDropdown($elementData);
            }
            $registerStart = '';
            $registerend = '';
            $regRequired = '';
            if ($isRequired) {
                $regRequired = '<span class="Form__Error">*</span>';
            }
            if ($isFieldRegister != '' && $isFieldRegister == 'yes') {
                $formGroupClassRegister = $this->getValue($elementData, 'form_register_class');
                $registerStart = '<div class="col-lg-6 col-md-6 ' .
                    $formGroupClassRegister . ' "><div class="custom-select2-div">
                              <div class="edit_inputMain-sltr select2Part w-100 floating-group">';
                $registerend = '</div></div></div>';
            }

            $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . ' ' . $formGroupClass . '">
                <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>
                ' . $multipleHiddenField . ' <select ' . $isMulti . ' ' . $onchange . ' ' . $isRequired .
                ' class="form-select ' . $class . '" ' . $extra . ' id="' . $name . '" name="' .
                $name . $isMultiPar . '" aria-label="Select ' . $label . '">
                                    ';

            ## Does Not Matteasdr :
            $doesNotMatter = _getConstant('common_label.DOESNT_MATTER');

            $optionValueStart = '<option ';
            $optionValueEnd = '</option>';
            $inputValue = ' value="';

            if ($displayPlaceholder == 'Yes') {
                $returnContent .= '<option selected value="" >Select ' . $label . $optionValueEnd;
            }


            $fields = [
                'part_caste',
                'part_state',
                'part_education',
                'part_occupation',
                'part_marital_status',
                'part_country',
                'part_religion',
                'part_mothertongue',
                'part_income',
                'part_diet',
                'part_smoke',
                'part_drink',
                'part_manglik',
            ];
            if (isset($name) && in_array($name, $fields)) {
                $selectedAny = in_array($doesNotMatter, $currentSelectedArra) ? 'selected' : '';
                $returnContent .= $optionValueStart . $selectedAny . $inputValue . $doesNotMatter . '">' . $doesNotMatter . $optionValueEnd;
            }

            if ($name === 'country_id' || $show_top_country_base == 'Yes') {
                $countryList = CountryMaster::active()
                    ->where('lang_code', _getDefaultLanguage())
                    ->orderByDesc('is_top_country')
                    ->orderBy('country_name')
                    ->get([
                        'id',
                        'country_name',
                        'is_top_country',
                    ]);
                $topCountries = $countryList->where('is_top_country', 1);
                $allCountries = $countryList->where('is_top_country', 0);
                /*
                |--------------------------------------------------------------------------
                | Top Countries
                |--------------------------------------------------------------------------
                */
                if ($topCountries->isNotEmpty()) {
                    $returnContent .= '<optgroup label="Top Countries">';
                    foreach ($topCountries as $country) {
                        $selectedDropdown = '';
                        if (in_array((string) $country->id, $currentSelectedArra)) {
                            $selectedDropdown = 'selected';
                        }
                        $returnContent .= '<option '
                            . $selectedDropdown
                            . ' value="' . e($country->id) . '">'
                            . e($country->country_name)
                            . '</option>';
                    }
                    $returnContent .= '</optgroup>';
                }
                /*
                |--------------------------------------------------------------------------
                | All Countries
                |--------------------------------------------------------------------------
                */
                if ($allCountries->isNotEmpty()) {
                    $returnContent .= '<optgroup label="All Countries">';
                    foreach ($allCountries as $country) {
                        $selectedDropdown = '';
                        if (in_array((string) $country->id, $currentSelectedArra)) {
                            $selectedDropdown = 'selected';
                        }
                        $returnContent .= '<option '
                            . $selectedDropdown
                            . ' value="' . e($country->id) . '">'
                            . e($country->country_name)
                            . '</option>';
                    }
                    $returnContent .= '</optgroup>';
                }
            } else {
                ## Existing Dropdown Logic :
                foreach ($valueArr as $key_r => $valueArrVal) {
                    $selectedDropdown = '';
                    if (in_array((string) $key_r, $currentSelectedArra)) {
                        $selectedDropdown = 'selected';
                    }
                    $returnContent .= $optionValueStart
                        . $selectedDropdown
                        . $inputValue
                        . e($key_r)
                        . '">'
                        . e($valueArrVal)
                        . $optionValueEnd;
                }
            }
            $returnContent .= '</select></div>' . $registerend;
        }
        return $returnContent;
    }

    ## Generate Relation DropDown :
    public function getRelationDropdown($elementData)
    {
        $returnArr = array();
        if (!empty($elementData)) {
            $currentValue = $this->getValue($elementData, 'value', '');
            $relationArr = $this->getValue($elementData, 'relation', '');
            $isMultiple = $this->getValue($elementData, 'is_multiple');
            $notLoadAdd = $this->getValue($relationArr, 'not_load_add', 'no');
            $notLoadAddSpecial = $this->getValue($relationArr, 'not_load_add_special', 'no');
            $language = $this->getValue($relationArr, 'lang', 'en');

            $mode = 'add';
            $rowData = [];
            if (isset($this->otherData['mode']) && $this->otherData['mode'] == 'edit') {
                $rowData = $this->otherData['rowData'];
                $mode = $this->otherData['mode'];
            }
            $returnValue = 'Yes';
            if (isset($notLoadAdd) && $notLoadAdd == 'yes' && $mode == 'add') {
                $returnValue = $returnArr;
            } elseif (isset($notLoadAddSpecial) && $notLoadAddSpecial == 'yes') {
                $returnValue = array();
            }

            if (empty($returnValue)) {
                return array();
            }
            if (!empty($relationArr) && isset($relationArr['rel_model']) && $relationArr['rel_model'] != '' && isset($relationArr['key_val']) && $relationArr['key_val'] != '' && isset($relationArr['key_disp']) && $relationArr['key_disp'] != '') {
                $whereClose = array();
                $whereInClose = array();
                $whereCloseStr = '';
                $statusField = 'status';
                $statusVal = 'APPROVED';
                if (isset($relationArr['status_filed']) && $relationArr['status_filed'] != '') {
                    $statusField = $relationArr['status_filed'];
                }
                if (isset($relationArr['status_val']) && $relationArr['status_val'] != '') {
                    $statusVal = $relationArr['status_val'];
                }

                if (isset($relationArr['cus_rel_col_name']) && $relationArr['cus_rel_col_name'] != '') {
                    $relationArr['rel_col_name'] = $relationArr['cus_rel_col_name'];
                }
                if (isset($relationArr['cus_rel_col_val']) && $relationArr['cus_rel_col_val'] != '' && isset($rowData[$relationArr['cus_rel_col_val']]) && $rowData[$relationArr['cus_rel_col_val']] != '') {
                    $relationArr['rel_col_val'] = $rowData[$relationArr['cus_rel_col_val']];
                }
                $relWhereArr = [];
                if (isset($relationArr['rel_col_name']) && $relationArr['rel_col_name'] != '') {
                    if (
                        !isset($relationArr['rel_col_val']) ||
                        $relationArr['rel_col_val'] == ''
                    ) {
                        $relColName = $relationArr['rel_col_name'];
                        if (isset($rowData[$relColName]) && $rowData[$relColName] != '') {
                            $relationArr['rel_col_val'] = $rowData[$relColName];
                        }
                    }

                    if (isset($relationArr['rel_col_val']) && $relationArr['rel_col_val'] != '') {
                        $return0 = 0;
                        if ($isMultiple != '' && $isMultiple == 'yes') {
                            $relValArrIn = explode(',', $relationArr['rel_col_val']);
                            $whereInClose[$relationArr['rel_col_name']] = $relValArrIn;
                            $return0 = 1;
                        } elseif (isset($relationArr['rel_col_val']) && is_array($relationArr['rel_col_val'])) {
                            $whereInClose[$relationArr['rel_col_name']] = $relationArr['rel_col_val'];
                            $return0 = 1;
                        } else {
                            $relWhereArr[$relationArr['rel_col_name']] = $relationArr['rel_col_val'];
                            $return0 = 1;
                        }
                        if ($return0 == 0) {
                            $returnArr;
                        }
                    } else {
                        return $returnArr;
                    }
                }

                if ($statusField != '' && $statusVal != '') {
                    $whereClose[] = $statusField . " = '" . $statusVal . "'";
                }
                if (!empty($whereClose)) {
                    $whereCloseStr = implode(" AND ", $whereClose);
                    $whereCloseStr = "( $whereCloseStr )";
                }

                $modelClass = $this->resolveModelClass($relationArr['rel_model']);
                $model = new $modelClass;
                $tableName = $model->getTable();
                $connection = $model->getConnectionName();
                $query = $modelClass::select('*');
                if ($whereCloseStr != '') {
                    $query = $query->whereRaw($whereCloseStr);
                }

                if (Schema::connection($connection)->hasColumn($tableName, 'lang_code')) {
                    $query->where('lang_code', _getDefaultLanguage());
                }
                if (!empty($relWhereArr)) {
                    $query = $query->where($relWhereArr);
                }
                if (!empty($whereInClose)) {
                    foreach ($whereInClose as $key => $value) {
                        $query = $query->whereIn($key, $value);
                    }
                }
                $rowDataArr = $query->orderByRaw('LOWER(' . $relationArr['key_disp'] . ') ASC')->get([$relationArr['key_val'], $relationArr['key_disp']]);

                $returnDataArr = [];
                $returnDataArr = $rowDataArr->toArray();
                foreach ($returnDataArr as $returnArrVal) {
                    $returnArrVal = (array)$returnArrVal;
                    $returnArr[$returnArrVal[$relationArr['key_val']]] =
                        $returnArrVal[$relationArr['key_disp']];
                }
            }
        }
        return $returnArr;
    }

    /**
     * Resolve model class safely
     */
    private function resolveModelClass(string $model): string
    {
        if (str_contains($model, '\\')) {
            return $model;
        }

        return "App\\Models\\{$model}";
    }

    ## Generate Mobile :
    public function generateMobile($elementData = [], $name = '')
    {
        $returnContent = '';
        if (!empty($elementData) && $name != '') {
            $currentValue = $this->getValue($elementData, 'value', '');
            $label = $this->getLabel($elementData, $name);
            $isRequired = $this->isRequired($elementData);
            $other = $this->getValue($elementData, 'other');
            $class = $this->getValue($elementData, 'class');
            $formGroupClass = $this->getValue($elementData, 'form_group_class');
            $placeHolder = $this->getValue($elementData, 'placeholder', $label);
            $isFieldRegister = $this->getValue($elementData, 'is_register');
            $alpNumStrFun = 'onkeypress="return isNumberKey(event)"';
            $columnClass = $this->getValue($elementData, 'column', '');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            $columnClass = $this->getValue($elementData, 'column', '');
            if (!blank($columnClass)) {
                $columnClass = 'col-md-' . $columnClass;
            }
            ## Tooltips info :
            $infoTooltip = $this->getValue($elementData, 'display_info', '');
            if (!blank($infoTooltip)) {
                $infoTooltip = '<a href="#" class="" data-bs-toggle="tooltip" data-bs-offset="0,4" data-bs-placement="top" data-bs-html="true" data-bs-original-title=\'<i class="bx bxs-info-circle"></i> <span>' . $infoTooltip . '</span>\'><i class="bx bxs-info-circle"></i></a>';
            }

            $countryCodeList = CountryMaster::active()
                ->whereNotNull('country_code')
                ->whereNotNull('country_name')
                ->selectRaw('
                    country_code,
                    MAX(country_name) as country_name,
                    MAX(is_top_country) as is_top_country
                ')
                ->groupBy('country_code')
                ->orderByDesc('is_top_country')
                ->orderBy('country_name')
                ->get();
            $topCountryCodes = $countryCodeList->where('is_top_country', 1);
            $allCountryCodes = $countryCodeList->where('is_top_country', 0);

            $configArr = _getSiteSetting();
            $currentCountCode = $configArr['default_country_code'];
            $mobileNumber = $currentValue;
            if ($currentValue != '') {
                $valueCurrArr = explode('-', $currentValue);
                if (count($valueCurrArr) == 2) {
                    $currentCountCode = $valueCurrArr[0];
                    $mobileNumber = $valueCurrArr[1];
                }
            }
            $registerStart = '';
            $registerend = '';
            $regRequired = '';
            if ($isRequired) {
                $regRequired = '<span class="Form__Error">*</span>';
            }
            if ($isFieldRegister != '' && $isFieldRegister == 'yes') {
                $registerStart = '<div class="col-lg-6 col-md-6"><div class="custom-select2-div">
                              <div class="edit_inputMain-sltr select2Part w-100 floating-group">';
                $registerend = '</div></div></div>';
            }
            $modeType = $this->getValue($elementData, 'modeType', '');
            $checkDisableDemoField = $this->getValue($elementData, 'isDisableInDemo', 'No');
            if (_getConstant('DISABLE_DEMO') == 'Enabled' && $modeType == 'edit' && $checkDisableDemoField == 'Yes') {
                $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . $formGroupClass . '">
                                <label class="form-label" for="' . $name . '">' . $label .
                    '</label>
                <h6>' . _getConstant('DISABLE_IN_DEMO_LABEL') . '</h6>';
            } else {
                $returnContent .= $registerStart . '<div class="mb-3 ' . $columnClass . $formGroupClass . '">
                    <label class="form-label" for="' . $name . '">' . $label . ' ' . $infoTooltip . ' ' . $regRequired . '</label>
                    <div class="row">
                        <div class="col-md-3">
                            <select ' . $isRequired . ' name="' . $name . '_country_code" class="form-select select2" id="' . $name . '_country_code">
                                <option value="">Select Country Code</option>';
                /*
                |--------------------------------------------------------------------------
                | Top Countries
                |--------------------------------------------------------------------------
                */
                if ($topCountryCodes->isNotEmpty()) {

                    $returnContent .= '<optgroup label="Top Countries">';

                    foreach ($topCountryCodes as $country) {

                        $selected = '';

                        if ((string) $country->country_code === (string) $currentCountCode) {
                            $selected = 'selected';
                        }

                        $returnContent .= '<option '
                            . $selected
                            . ' value="' . e($country->country_code) . '">'
                            . e($country->country_code)
                            . '</option>';
                    }

                    $returnContent .= '</optgroup>';
                }


                /*
                |--------------------------------------------------------------------------
                | All Countries
                |--------------------------------------------------------------------------
                */
                if ($allCountryCodes->isNotEmpty()) {

                    $returnContent .= '<optgroup label="All Countries">';

                    foreach ($allCountryCodes as $country) {

                        $selected = '';

                        if ((string) $country->country_code === (string) $currentCountCode) {
                            $selected = 'selected';
                        }

                        $returnContent .= '<option '
                            . $selected
                            . ' value="' . e($country->country_code) . '">'
                            . e($country->country_code)
                            . '</option>';
                    }

                    $returnContent .= '</optgroup>';
                }
            }

            if (_getConstant('DISABLE_DEMO') == 'Enabled' && $modeType == 'edit' && $checkDisableDemoField == 'Yes') {
                $returnContent .= '
                                </div>' . $registerend;
            } else {
                $returnContent .= '</select>
                                        </div>
                                        <div class="col-md-9">
                                            <input  ' . $other . ' ' . $alpNumStrFun .
                    'type="number" ' . $isRequired . ' class="form-control ' . $class .
                    '" minlength="7" maxlength="13" data-maxlength="13" name="' . $name .
                    '" id="' . $name . '" onchange="check_valid_number(this);" placeholder="' .
                    $placeHolder . '" value ="' . htmlentities(stripcslashes($mobileNumber)) . '"  />
                                        </div>
                                    </div>
                                </div>' . $registerend;
            }
        }
        return $returnContent;
    }

    ## Get Value :
    public function getValue($elementData, $key = 'value', $defult = '')
    {
        $currentVal = $defult;
        if (isset($elementData[$key]) && $elementData[$key] != '') {
            $currentVal = $elementData[$key];
        }
        return $currentVal;
    }

    ## Get Label :
    public function getLabel($elementData = [], $name = '')
    {
        $label = '';
        if (isset($elementData['label']) && $elementData['label'] != '') {
            $label = $elementData['label'];
        } else {
            $label = str_replace('_', ' ', $name);
            $label = ucwords($label);
        }
        return $label;
    }

    ## Check Is REquired :
    public function isRequired($elementData)
    {
        $isRequired = "";
        if (isset($elementData['is_required']) && $elementData['is_required'] != '' && $elementData['is_required'] == 'required') {
            $isRequired = " required ";
        }
        return $isRequired;
    }
}
