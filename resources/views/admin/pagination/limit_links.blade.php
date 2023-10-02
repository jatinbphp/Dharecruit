
<?php
// config
$link_limit = 7; // maximum number of links (a little bit inaccurate, but will be ok for now)
if(isset($paginator) && $paginator != NULL){
    $paginator->appends(request()->input())->links();
}
?>

@if (isset($paginator) && $paginator->lastPage() > 1)
    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
        <ul class="pagination">
            <li class="paginate_button page-item previous {{ ($paginator->currentPage() == 1) ? ' disabled' : '' }}" id="example2_previous">
                <a href="{{ $paginator->url(1) }}" aria-controls="example2" data-dt-idx="0" tabindex="0" class="page-link">First</a>
            </li>

            @for ($i = 1; $i <= $paginator->lastPage(); $i++)
                <?php
                $half_total_links = floor($link_limit / 2);
                $from = $paginator->currentPage() - $half_total_links;
                $to = $paginator->currentPage() + $half_total_links;
                if ($paginator->currentPage() < $half_total_links) {
                    $to += $half_total_links - $paginator->currentPage();
                }
                if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                    $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
                }
                ?>
                @if ($from < $i && $i < $to)
                    <li class="paginate_button page-item ">
                        <a href="{{ $paginator->url($i) }}"></a>
                    </li>
                    <li class="paginate_button page-item {{ ($paginator->currentPage() == $i) ? ' active' : '' }}">
                        <a href="{{ $paginator->url($i) }}" aria-controls="example2" data-dt-idx="1" tabindex="0" class="page-link">{{ $i }}</a>
                    </li>
                @endif
            @endfor

            <li class="paginate_button page-item next {{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : '' }}" id="example2_next">
                <a href="{{ $paginator->url($paginator->lastPage()) }}" aria-controls="example2" data-dt-idx="7" tabindex="0" class="page-link">Last</a>
            </li>
        </ul>
    </div>
@endif