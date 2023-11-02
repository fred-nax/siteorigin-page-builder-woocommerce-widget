<?php

class Widget_Produits_WooCommerce extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'widget_produits_woocommerce',
            'Custom product WooCommerce naxialis',
            array(
                'description' => 'Affiche un produit WooCommerce.'
            )
        );
    }

    public function widget($args, $instance) {
        // Récupérer l'ID du produit sélectionné depuis les paramètres du widget
        $product_id = $instance['product_id'];

        echo $args['before_widget'];

        // Récupérer le produit WooCommerce sélectionné
        $product = wc_get_product($product_id);

        if ($product) {
            echo '<div class="woocommerce-product-stage">';
            echo $args['before_title'] . $product->get_name() . $args['after_title'];

            // Afficher l'image du produit
            $image = $product->get_image();
            echo '<div class="product-image">' . $image . '</div>';

            // Afficher le prix du produit
            if ($product->get_price()) {
                echo '<div class="product-price">';
                echo wc_price($product->get_price());
                echo '</div>';
            }

            // Bouton "Ajouter au panier" ou "Voir le produit" en fonction de l'option sélectionnée
            $button_type = $instance['button_type'];
            echo '<div class="product-button">';
            if ($button_type === 'add_to_cart') {
                echo '<a href="' . esc_url($product->add_to_cart_url()) . '" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="' . $product->get_id() . '" data-product_sku="' . esc_attr($product->get_sku()) . '">' . esc_html__('Ajouter au panier', 'woocommerce') . '</a>';
            } elseif ($button_type === 'view_product') {
                echo '<a href="' . esc_url(get_permalink($product->get_id())) . '" class="button product_type_simple" data-product_id="' . $product->get_id() . '">' . esc_html__('Voir le produit', 'woocommerce') . '</a>';
            }
            echo '</div>';

            echo '</div>';
        } else {
            echo 'Produit introuvable.';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : 'Produit WooCommerce';
        $product_id = !empty($instance['product_id']) ? $instance['product_id'] : 0;
        $button_type = !empty($instance['button_type']) ? $instance['button_type'] : 'add_to_cart';
        ?>

        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Titre :</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('product_id'); ?>">Sélectionnez le produit à afficher :</label>
            <?php
            // Afficher un champ de sélection de produits WooCommerce
            $args = array(
                'post_type' => 'product',
                'posts_per_page' => -1,
            );

            $products = new WP_Query($args);

            echo '<select id="' . $this->get_field_id('product_id') . '" name="' . $this->get_field_name('product_id') . '">';
            echo '<option value="0">Sélectionnez un produit</option>';

            while ($products->have_posts()) {
                $products->the_post();
                $product = wc_get_product(get_the_ID());
                $selected = selected($product_id, $product->get_id(), false);
                echo '<option value="' . $product->get_id() . '" ' . $selected . '>' . $product->get_name() . '</option>';
            }

            echo '</select>';
            wp_reset_postdata();
            ?>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id('button_type'); ?>">Sélectionnez le bouton à afficher :</label>
            <select id="<?php echo $this->get_field_id('button_type'); ?>" name="<?php echo $this->get_field_name('button_type'); ?>">
                <option value="add_to_cart" <?php selected($button_type, 'add_to_cart'); ?>>Ajouter au panier</option>
                <option value="view_product" <?php selected($button_type, 'view_product'); ?>>Voir le produit</option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? strip_tags($new_instance['title']) : 'Produit WooCommerce';
        $instance['product_id'] = !empty($new_instance['product_id']) ? $new_instance['product_id'] : 0;
        $instance['button_type'] = !empty($new_instance['button_type']) ? $new_instance['button_type'] : 'add_to_cart';

        return $instance;
    }
}

function register_widget_produits_woocommerce() {
    register_widget('Widget_Produits_WooCommerce');
}

add_action('widgets_init', 'register_widget_produits_woocommerce');
