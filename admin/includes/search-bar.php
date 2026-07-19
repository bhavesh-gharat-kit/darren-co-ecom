<?php
/**
 * search-bar.php - Reusable Search Bar Component
 */

function renderSearchBar($options = []) {
    
    // Default options
    $defaults = [
        'search' => isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '',
        'placeholder' => 'Search by name or description...',
        'submit_text' => 'Search',
        'clear_text' => 'Clear',
        'show_clear' => true,
        'class' => 'mb-6 bg-white rounded-lg shadow p-4',
        'input_class' => 'w-full border rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500',
        'button_class' => 'bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium',
        'clear_class' => 'bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium',
        'action' => ''
    ];
    
    $options = array_merge($defaults, $options);
    $search = $options['search'];
    $action = $options['action'] ?: $_SERVER['PHP_SELF'];
    
    ob_start();
    ?>
    
    <div class="<?= htmlspecialchars($options['class']) ?>">
        <form method="GET" action="<?= htmlspecialchars($action) ?>" class="flex gap-4 flex-wrap">
            
            <div class="flex-1 min-w-[200px]">
                <input type="text" 
                       name="search" 
                       value="<?= $search ?>"
                       placeholder="<?= htmlspecialchars($options['placeholder']) ?>"
                       class="<?= htmlspecialchars($options['input_class']) ?>"
                       autocomplete="off">
            </div>
            
            <button type="submit" class="<?= htmlspecialchars($options['button_class']) ?>">
                🔍 <?= htmlspecialchars($options['submit_text']) ?>
            </button>
            
            <?php if ($options['show_clear'] && !empty($search)): ?>
                <a href="<?= htmlspecialchars($action) ?>" 
                   class="<?= htmlspecialchars($options['clear_class']) ?>">
                    ✕ <?= htmlspecialchars($options['clear_text']) ?>
                </a>
            <?php endif; ?>
            
            <?php
            // 🔥 FIX: Properly preserve GET parameters
            $params = $_GET;
            unset($params['search']);
            
            foreach ($params as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $sub_value) {
                        echo '<input type="hidden" name="' . htmlspecialchars($key) . '[]" value="' . htmlspecialchars($sub_value) . '">';
                    }
                } else {
                    echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
                }
            }
            ?>
            
        </form>
    </div>
    
    <?php
    return ob_get_clean();
}
?>