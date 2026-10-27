<?php
// සිංහල සටහන: external emoji නොව local SVG modern icons මෙතනින් render කරයි.
function icon_svg(string $name, string $class = 'icon'): string
{
    $icons = [
        'menu' => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'cart' => '<path d="M6 6h15l-1.5 9h-12z"/><path d="M6 6 5 3H2"/><circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/>',
        'user' => '<circle cx="12" cy="8" r="4"/><path d="M4 21c1.8-4.2 14.2-4.2 16 0"/>',
        'arrow' => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'star' => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.1L12 17.2 6.4 20.1 7.5 14 3 9.6l6.2-.9z"/>',
        'box' => '<path d="M21 8 12 3 3 8l9 5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>',
        'mail' => '<path d="M4 6h16v12H4z"/><path d="m4 7 8 6 8-6"/>',
        'chart' => '<path d="M4 20V4"/><path d="M4 20h16"/><path d="M8 16v-5"/><path d="M12 16V8"/><path d="M16 16v-9"/>',
        'shield' => '<path d="M12 3 5 6v5c0 4.5 2.8 8.5 7 10 4.2-1.5 7-5.5 7-10V6z"/>',
        'edit' => '<path d="M4 20h4l11-11-4-4L4 16z"/><path d="m14 6 4 4"/>',
        'trash' => '<path d="M4 7h16"/><path d="M9 7V4h6v3"/><path d="M6 7l1 13h10l1-13"/>',
        'plus' => '<path d="M12 5v14"/><path d="M5 12h14"/>',
    ];
    $path = $icons[$name] ?? $icons['star'];
    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}
