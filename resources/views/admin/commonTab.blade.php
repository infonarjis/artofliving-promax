<div class="tabListing_topnav">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        @if (isset($statusTabArr) && !empty($statusTabArr))
            @foreach($statusTabArr as $key => $tab)
            <li class="nav-item" role="">
                <button class="tabClick nav-link {{ $tab['class']  ?? ''}}
                    {{ (isset($tab['isActive']) && $tab['isActive'] == 1) ? 'active' : '' }} "
                    data-conditionVal="{{ $tab['conditionVal'] }} "
                    data-conditionColumn="{{ $tab['conditionColumn'] }}" id="{{ $tab['id'] }}" type="button">{{
                    $tab['label'] }} (<label class="countRecord">0</label>)
                </button>
            </li>
        @endforeach
        @endif
    </ul>
</div>