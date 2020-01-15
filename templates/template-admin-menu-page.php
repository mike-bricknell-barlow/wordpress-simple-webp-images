<div class="wrap">
    <h1>Simple Webp Images - Settings</h1>

    <form method="post" action="<?php echo admin_url ( 'admin-post.php' ) ?>"> 
        <?php settings_fields( 'simple-webp-images-options-group' ); ?>
        <input type="hidden" name="action" value="update_settings">
        <table class="form-table">
            <tbody>
                <?php 
                
                foreach ( $fields as $field ) {
                    echo '<tr>';
                        switch ( $field['type'] ) {
                            case 'text':
                                ?>

                                <th><label for="<?php echo esc_html( $field['id'] ) ?>"><?php echo esc_html( $field['label'] ) ?></label></th>
                                <td>
                                    <input type="text" id="<?php echo esc_html( $field['id'] ) ?>" name="<?php echo esc_html( $field['id'] ) ?>" placeholder="80" value="<?php echo esc_html( $field['value'] ) ?>" />
                                    <?php if ( $field['description'] ): ?>
                                        <p><?php echo esc_html( $field['description'] ) ?></p>
                                    <?php endif; ?>
                                </td>

                                <?php
                                break;

                            case 'checkbox':
                                ?>

                                <th><label for="<?php echo esc_html( $field['id'] ) ?>"><?php echo esc_html( $field['label'] ) ?></label></th>
                                <td>
                                    <input type="checkbox" id="<?php echo esc_html( $field['id'] ) ?>" name="<?php echo esc_html( $field['id'] ) ?>" <?php echo ( $field['value'] == 'on' ) ? esc_html( 'checked="checked"' ) : '' ?> />
                                    <?php if ( $field['description'] ): ?>
                                        <p><?php echo esc_html( $field['description'] ) ?></p>
                                    <?php endif; ?>
                                </td>

                                <?php
                                break;
                        }
                    echo '</tr>';
                }
                
                ?>
            </tbody>
        </table>   
        <?php submit_button(); ?> 

        <hr />
        <h2>Bulk Convert</h2>

        <p>Use the button below to convert all images in the media library, including the resized versions, to .wepb.</p>
        <p>If you've changed the quality settings above, you will need to run the bulk conversion to generate images with the new quality setting.</p>

        <button class="button button-primary" id="start-bulk-conversion">
            Start bulk conversion
        </button>

        <p class="hidden step step-2">Counting images...</p>

        <p class="hidden step step-3">Images converted: <span id="remaining-images">0</span>/<span id="total-images"></span></p>
    
        <p class="hidden step step-3 converting">Images are now being converted. Please leave this browser window open until the conversion has completed.</p>
    
        <p class="hidden step step-4">Bulk conversion is now complete.</p>
    </form>
</div>
