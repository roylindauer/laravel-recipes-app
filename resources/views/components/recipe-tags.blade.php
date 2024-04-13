<div class="py-3">
    <?php
    if (!empty($tags)):
    echo Arr::join(Arr::map($tags, function($key){
        return "<span class='inline-block bg-gray-200 dark:bg-gray-600 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2 mr-1'>{$key}</span>";
    }), " ");
    endif;
    ?>
</div>
