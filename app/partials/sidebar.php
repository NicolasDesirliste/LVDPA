<?php
// app/views/partials/sidebar.php
?>
<aside class="sidebar">
    <ul class="menu-list">
        <?php if (isset($sidebarMenus) && !empty($sidebarMenus)): ?>
            <?php foreach ($sidebarMenus as $menu): ?>
                <li class="menu-item">
                    <?php echo htmlspecialchars($menu['title']); ?>
                    <div class="sidebar-tooltip">
                        <div class="tooltips-header">
                            <h6><?php echo htmlspecialchars($menu['tooltip_header']); ?></h6>
                        </div>
                        
                        <?php foreach ($menu['links'] as $link): ?>
                            <a href="<?php echo htmlspecialchars($link['href']); ?>" 
                               class="ff-button<?php echo isset($link['class']) ? ' ' . htmlspecialchars($link['class']) : ''; ?>"
                               <?php if (isset($link['spa']) && $link['spa']): ?>
                                   data-spa-link
                               <?php endif; ?>
                               <?php if (isset($link['id'])): ?>
                                   id="<?php echo htmlspecialchars($link['id']); ?>"
                               <?php endif; ?>>
                                <?php echo htmlspecialchars($link['text']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Message si aucun menu n'est disponible (ne devrait pas arriver) -->
            <li class="menu-item">
                <span>Aucun menu disponible</span>
            </li>
        <?php endif; ?>
    </ul>
</aside>