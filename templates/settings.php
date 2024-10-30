<link rel="stylesheet" type="text/css" href="<?php echo plugins_url( 'styles/settings.css', dirname( __FILE__ ) ) ?>">

<?php
$momently_site_id             = get_option( 'momently_site_id' );
$momently_track_admin         = get_option( 'momently_track_admin', 1 );
$momently_automatic_updates   = get_option( 'momently_automatic_updates', 1 );
$momently_custom_taxonomy_cat = get_option( 'momently_custom_taxonomy_cat', 'category' );
$momently_top_level_cat       = get_option( 'momently_top_level_cat', 1 );
$momently_cats_as_tags        = get_option( 'momently_cats_as_tags');
$momently_lowercase_tags      = get_option( 'momently_lowercase_tags',1 );
?>
<div class="wrap">
    <h1>Momently Settings</h1>
    <br><br>
    <form name="form" method="post" action="options.php">
		<?php
		settings_fields( 'momently-options' );
		?>
        <table class="form-table">
            <tbody>
            <tr>
                <th><label for="momently_site_id">Site ID</label></th>
                <td><input name="momently_site_id" id="momently_site_id" type="text"
                           value="<?php echo $momently_site_id ?>" required class="regular-text">
                    <div id="momently_select_another" class="button button-secondary">Reset Site ID</div>
                </td>
            </tr>
            <tr>
                <th><label>Custom taxonomy for section</label></th>
                <td class="checkbox-td">
                    <table>
                        <tr>
                            <td>
                                <input name="momently_custom_taxonomy_cat" id="momently_custom_taxonomy_cat" type="text"
                                       value="<?php echo $momently_custom_taxonomy_cat ?>" required class="regular-text">
                                <br><br>
                                <div class="mt-help-text">
                                    <p class="description">Momently uses post's category for section name. If you have created a custom taxonomy,
                                        and want to use that instead of category, you can change it here.</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th><label>Use Top level category</label></th>
                <td class="checkbox-td">
                    <table>
                        <tr>
                            <td>
                                <div class="button-primary mt-switchoo">
                                    <input type="checkbox" name="momently_top_level_cat" value="1"
                                           class="mt-switchoo-checkbox"
                                           onchange="this.form.submit()"
                                           id="momently_top_level_cat" <?php checked( $momently_top_level_cat, 1 ); ?>>
                                    <label class="mt-switchoo-label" for="momently_top_level_cat">
                                        <div class="mt-switchoo-inner"></div>
                                        <div class="mt-switchoo-switch"></div>
                                    </label>
                                </div>
                                <br><br>
                                <div class="mt-help-text">
                                    <p class="description">When this option is set, Momently uses the parent category as section. For example, if you publish a story under Opinion > Social commentary > Free press, Momently will use the "Opinion" for section name in your dashboard instead of "Free press".</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th><label>Add categories to tags</label></th>
                <td class="checkbox-td">
                    <table>
                        <tr>
                            <td>
                                <div class="button-primary mt-switchoo">
                                    <input type="checkbox" name="momently_cats_as_tags" value="1"
                                           class="mt-switchoo-checkbox"
                                           onchange="this.form.submit()"
                                           id="momently_cats_as_tags " <?php checked( $momently_cats_as_tags , 1 ); ?>>
                                    <label class="mt-switchoo-label" for="momently_cats_as_tags ">
                                        <div class="mt-switchoo-inner"></div>
                                        <div class="mt-switchoo-switch"></div>
                                    </label>
                                </div>
                                <br><br>
                                <div class="mt-help-text">
                                    <p class="description">Add all assigned categories and taxonomies to tags field.</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th><label>Lowercase tags</label></th>
                <td class="checkbox-td">
                    <table>
                        <tr>
                            <td>
                                <div class="button-primary mt-switchoo">
                                    <input type="checkbox" name="momently_lowercase_tags" value="1"
                                           class="mt-switchoo-checkbox"
                                           onchange="this.form.submit()"
                                           id="momently_lowercase_tags " <?php checked( $momently_lowercase_tags , 1 ); ?>>
                                    <label class="mt-switchoo-label" for="momently_lowercase_tags ">
                                        <div class="mt-switchoo-inner"></div>
                                        <div class="mt-switchoo-switch"></div>
                                    </label>
                                </div>
                                <br><br>
                                <div class="mt-help-text">
                                    <p class="description">By default Momently uses lowercase version of tags to avoid any misspellings. If you disable this option, tags will be used as they are.</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th><label>Track Logged-in Users</label></th>
                <td class="checkbox-td">
                    <table>
                        <tr>
                            <td>
                                <div class="button-primary mt-switchoo">
                                    <input type="checkbox" name="momently_track_admin" value="1"
                                           class="mt-switchoo-checkbox"
                                           onchange="this.form.submit()"
                                           id="momently_track_admin" <?php checked( $momently_track_admin, 1 ); ?>>
                                    <label class="mt-switchoo-label" for="momently_track_admin">
                                        <div class="mt-switchoo-inner"></div>
                                        <div class="mt-switchoo-switch"></div>
                                    </label>
                                </div>
                                <br><br>
                                <div class="mt-help-text">
                                    <p class="description">By default, Momently will track the activity of users
                                        that are logged into this site. You can change this setting to only track
                                        the activity of anonymous visitors. Note: You will no longer see the
                                        Momently tracking code on your site if you browse while logged in.</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <th><label>Auto update</label></th>
                <td class="checkbox-td">
                    <table>
                        <tr>
                            <td>
                                <div class="button-primary mt-switchoo">
                                    <input type="checkbox" name="momently_automatic_updates" value="1"
                                           class="mt-switchoo-checkbox"
                                           onchange="this.form.submit()"
                                           id="momently_automatic_updates" <?php checked( $momently_automatic_updates, 1 ); ?>>
                                    <label class="mt-switchoo-label" for="momently_automatic_updates">
                                        <div class="mt-switchoo-inner"></div>
                                        <div class="mt-switchoo-switch"></div>
                                    </label>
                                </div>
                                <br><br>
                                <div class="mt-help-text">
                                    <p class="description">Momently will automatically update the plugin when a new
                                        version is available. You can disable the automatic update here.</p>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
        <table>
            <tbody>
            <tr>
                <td colspan="2" class="submit">
                    <input type="submit" name="Submit" class="button button-primary" value="Save Changes">
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>



