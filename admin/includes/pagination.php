<?php
/**
 * pagination.php - Reusable Pagination System
 */

function getPagination($total_items, $items_per_page = 10) {
    
    $current_page = isset($_GET['page']) && is_numeric($_GET['page']) 
                    ? (int)$_GET['page'] 
                    : 1;
    $current_page = max(1, $current_page);
    
    $total_pages = ($items_per_page > 0) ? ceil($total_items / $items_per_page) : 1;
    
    if ($current_page > $total_pages && $total_pages > 0) {
        $current_page = $total_pages;
    }
    
    $offset = ($current_page - 1) * $items_per_page;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    // 🔥 FIX: Build query params without array values
    $query_params = [];
    
    // Add search if exists
    if (!empty($search)) {
        $query_params['search'] = $search;
    }
    
    // Add other parameters (skip 'page' and skip array values)
    foreach ($_GET as $key => $value) {
        if ($key !== 'page' && $key !== 'search' && !is_array($value)) {
            $query_params[$key] = $value;
        }
    }
    
    return [
        'current_page' => $current_page,
        'total_pages' => $total_pages,
        'total_items' => $total_items,
        'items_per_page' => $items_per_page,
        'offset' => $offset,
        'search' => $search,
        'query_params' => $query_params,
        'has_pages' => $total_pages > 1,
        'start_number' => $offset + 1,
        'end_number' => min($offset + $items_per_page, $total_items)
    ];
}

function renderPagination($pagination, $options = []) {
    
    $defaults = [
        'show_first_last' => true,
        'show_numbers' => true,
        'show_items_per_page' => false,
        'show_info' => true,
        'range' => 2,
        'container_class' => 'flex items-center justify-between px-6 py-4 border-t flex-wrap gap-4',
        'items_per_page_options' => [5, 10, 25, 50]
    ];
    
    $options = array_merge($defaults, $options);
    
    $current_page = $pagination['current_page'];
    $total_pages = $pagination['total_pages'];
    $total_items = $pagination['total_items'];
    $items_per_page = $pagination['items_per_page'];
    $query_params = $pagination['query_params'];
    
    if (!$pagination['has_pages'] || $total_items == 0) {
        return '';
    }
    
    // 🔥 FIX: Build URL without array values
    $buildUrl = function($page) use ($query_params) {
        $params = $query_params;
        $params['page'] = $page;
        return '?' . http_build_query($params);
    };
    
    ob_start();
    ?>
    
    <div class="<?= htmlspecialchars($options['container_class']) ?>">
        
        <?php if ($options['show_info']): ?>
            <div class="text-sm text-gray-600">
                Showing <?= $pagination['start_number'] ?> to <?= $pagination['end_number'] ?> 
                of <?= $total_items ?> entries
            </div>
        <?php endif; ?>
        
        <div class="flex items-center gap-2 flex-wrap">
            
            <?php if ($options['show_first_last']): ?>
                <a href="<?= $buildUrl(1) ?>" 
                   class="px-3 py-2 rounded border text-sm <?= $current_page == 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-blue-500 hover:text-white' ?>">
                    First
                </a>
            <?php endif; ?>
            
            <a href="<?= $buildUrl(max(1, $current_page - 1)) ?>" 
               class="px-3 py-2 rounded border text-sm <?= $current_page == 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-blue-500 hover:text-white' ?>">
                ← Prev
            </a>
            
            <?php if ($options['show_numbers']): ?>
                <?php
                $start = max(1, $current_page - $options['range']);
                $end = min($total_pages, $current_page + $options['range']);
                
                if ($start > 1) {
                    echo '<span class="px-2 text-gray-500">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <a href="<?= $buildUrl($i) ?>" 
                       class="px-3 py-2 rounded border text-sm <?= $i == $current_page ? 'bg-blue-600 text-white' : 'hover:bg-blue-500 hover:text-white' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($end < $total_pages): ?>
                    <span class="px-2 text-gray-500">...</span>
                <?php endif; ?>
            <?php endif; ?>
            
            <a href="<?= $buildUrl(min($total_pages, $current_page + 1)) ?>" 
               class="px-3 py-2 rounded border text-sm <?= $current_page == $total_pages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-blue-500 hover:text-white' ?>">
                Next →
            </a>
            
            <?php if ($options['show_first_last']): ?>
                <a href="<?= $buildUrl($total_pages) ?>" 
                   class="px-3 py-2 rounded border text-sm <?= $current_page == $total_pages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'hover:bg-blue-500 hover:text-white' ?>">
                    Last
                </a>
            <?php endif; ?>
            
        </div>
        
        <?php if ($options['show_items_per_page']): ?>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select onchange="window.location.href='?' + new URLSearchParams({
                    ...Object.fromEntries(new URLSearchParams(window.location.search)),
                    limit: this.value,
                    page: 1
                }).toString()"
                        class="border rounded px-2 py-1 text-sm">
                    <?php foreach ($options['items_per_page_options'] as $option): ?>
                        <option value="<?= $option ?>" <?= $items_per_page == $option ? 'selected' : '' ?>>
                            <?= $option ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        
    </div>
    
    <?php
    return ob_get_clean();
}
?>