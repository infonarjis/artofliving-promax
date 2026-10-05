@if($activeLayouts->id == '1')
    @include(_getConstant('dir_path.WEB_DIR_PATH').'.memberCardLayouts.layout1', ['result' => $result])
@elseif($activeLayouts->id == '2')
    @include(_getConstant('dir_path.WEB_DIR_PATH').'.memberCardLayouts.layout2', ['result' => $result])
@elseif($activeLayouts->id == '3')
    @include(_getConstant('dir_path.WEB_DIR_PATH').'.memberCardLayouts.layout3', ['result' => $result])
@endif