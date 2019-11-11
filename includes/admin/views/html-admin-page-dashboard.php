<div class="ui basic vertical segment">
    <div class="ui grid">
        <div class="ten wide column">
            <h2 class="ui header">
                <?php echo get_the_post_thumbnail( $default_club, 'club_single', array( 'class' => 'ui image' ) ); ?>
                <div class="content">
                    <?php _e( 'Team Overview', 'wp-club-manager' ); ?> 
                    <div class="sub header"><?php echo ( $team_name ? $team_name . ' &mdash; ' : '' ) . $season->name; ?></div>
                </div>
            </h2>
        </div>
        <?php
        if( $team ) { ?>
            <div class="right aligned six wide column">
                <form action="" method="post" class="ui form">
                    <div class="inline field">
                        <label for="team_select"><?php _e( 'Choose Team', 'wp-club-manager' ); ?></label>

                        <?php
                        $args = array(
                            'taxonomy' => 'wpcm_team',
                            'name' => 'team_select',
                            'value_field' => 'term_id',
                            'orderby' => 'tax_position',
                            'meta_key' => 'tax_position',
                            'class' => 'ui dropdown',
                            'selected' => $team,
                            'hide_empty' => false,
                        );
                        wp_dropdown_categories( $args );
                        ?>

                        <button class="ui button switch-team">
                            <?php _e( 'Apply', 'wp-club-manager' ); ?>
                        </button>
                    </div>
                </form>
            </div>
        <?php
        } ?>
    </div>
</div>

<div class="ui basic vertical segment">

    <div class="ui one column grid">

        <div class="column">
            <div class="ui fluid card">
                <div class="content">
                    <div class="header">
                        <?php _e( 'Performance Stats', 'wp-club-manager' ); ?>
                    </div>
                    <div class="meta"><?php _e( 'All competitions', 'wp-club-manager' ); ?></div>
                </div>
                <div class="content">
                    
                        <div class="ui four statistics">
                            <div class="green statistic">
                                <div class="label">
                                    <?php _e( 'Win Percentage', 'wp-club-manager' ); ?>
                                </div>
                                <div class="value">
                                    <?php echo $win_percent; ?>
                                </div>
                            </div>
                            <div class="orange statistic">
                                <div class="label">
                                <?php _e( 'Biggest Win', 'wp-club-manager' ); ?>
                                </div>
                                <div class="value">
                                    <?php echo $biggest_score; ?>
                                </div>
                            </div>
                            <div class="teal statistic">
                                <div class="label">
                                <?php echo $f_label ?>
                                </div>
                                <div class="value">
                                    <?php echo $goals_scored; ?>
                                </div>
                            </div>
                            <div class="red statistic">
                                <div class="label">
                                <?php echo $a_label ?>
                                </div>
                                <div class="value">
                                <?php echo $goals_conceded; ?>
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>

    </div>

</div>

<div class="ui basic vertical segment">

    
        <div class="ui stackable three column grid">
            <div class="column">
                <div class="ui fluid card">
                    <div class="content">
                        <div class="header"><?php _e( 'Latest Results', 'wp-club-manager' ); ?></div>
                    </div>
                    <div class="content">

                        <?php
                        if( $played_matches ) { ?>

                            <div class="ui very relaxed list">
                                
                            <?php foreach( $played_matches as $played_match ) {

                                $played = get_post_meta( $played_match->ID, 'wpcm_played', true );
                                $timestamp = strtotime( $played_match->post_date );
                                $venue = wpcm_get_match_venue( $played_match->ID );
                                $team = wpcm_get_match_team( $played_match->ID );
                                $comp = wpcm_get_match_comp( $played_match->ID );
                                $result = wpcm_get_match_result( $played_match->ID );
                                $opponent = wpcm_get_match_opponents( $played_match->ID, false );
                                $class = wpcm_get_match_outcome( $played_match->ID );
                                if( $class == 'win' ) {
                                    $class = 'green';
                                }elseif( $class == 'loss' ) {
                                    $class = 'red';
                                }elseif( $class == 'draw' ) {
                                    $class = 'yellow';
                                }
                                ?>


                                <div class="item">
                                    <?php if( $played ) { ?>
                                        <div class="right floated content">
                                                <div class="ui medium <?php echo $class; ?> label"><?php echo $result[0]; ?></div>
                                        </div>
                                    <?php } else { ?>
                                        <div class="right floated content">
                                            <div class="ui medium grey label"><?php _ex( 'TBC', 'To be confirmed', 'wp-club-manager' ); ?></div>
                                        </div>
                                    <?php } ?>
                                    <div class="content">
                                        <div class="meta">
                                            <span class="date"><?php echo date_i18n( 'D d M', $timestamp ); ?></span>
                                            <span class="venue"><?php echo $venue['name']; ?></span>
                                        </div>
                                        <a class="header" href="<?php echo get_edit_post_link( $played_match->ID ); ?>"><?php echo $opponent; ?></a>
                                        <div class="meta">
                                            <span class="competition"><?php echo $comp[0]; ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                            <?php
                            } ?>
                                
                        </div>

                        <?php
                        } else { ?>

                            <div class="ui warning message">
                                <p><?php _e( 'There are no results yet.', 'wp-club-manager' ); ?></p>
                            </div>
                        
                        <?php
                        } ?>

                    </div>
                    <div class="extra content">
                        <div class="ui two buttons">
                            <a class="ui basic button" href="<?php echo $new_query; ?>"><?php _e( 'Manage matches', 'wp-club-manager' ); ?></a>
                            <a class="ui basic button" href="<?php echo admin_url( 'post-new.php?post_type=wpcm_match' ); ?>"><?php _e( 'Add new match', 'wp-club-manager' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="column">
                    <div class="ui fluid card">
                        <div class="content">
                            <div class="header"><?php _e( 'Next Fixtures', 'wp-club-manager' ); ?></div>
                        </div>
                        <div class="content">

                            <?php
                            if( $future_matches ) { ?>

                                <div class="ui very relaxed list">

                                <?php foreach( $future_matches as $future_match ) {

                                    $played = get_post_meta( $future_match->ID, 'wpcm_played', true );
                                    $timestamp = strtotime( $future_match->post_date );
                                    $time_format = get_option( 'time_format' );
                                    $venue = wpcm_get_match_venue( $future_match->ID );
                                    $team = wpcm_get_match_team( $future_match->ID );
                                    $comp = wpcm_get_match_comp( $future_match->ID );
                                    $fixture = wpcm_get_match_result( $future_match->ID );
                                    $opponent = wpcm_get_match_opponents( $future_match->ID, false );
                                    $class = wpcm_get_match_outcome( $future_match->ID );
                                    ?>

                                    <div class="item">
                                            <div class="right floated content">
                                                    <div class="ui medium basic label"><?php echo date_i18n( $time_format, $timestamp ); ?></div>
                                            </div>
                                        <!-- <img class="ui middle aligned image" src="images/kingsdownracersfc-27x34.jpg"> -->
                                        <div class="content">
                                                <div class="meta">
                                                    <span class="date"><?php echo date_i18n( 'D d M', $timestamp ); ?></span>
                                                    <span class="venue"><?php echo $venue['name']; ?></span>
                                                    </div>
                                            <a class="header" href="<?php echo get_edit_post_link( $future_match->ID ); ?>"><?php echo $opponent; ?></a>
                                            <div class="meta">
                                                <span class="competition"><?php echo $comp[0]; ?></span>
                                            </div>
                                        </div>
                                    </div>

                                <?php
                                } ?>

                                </div>
                            
                            <?php
                            } else { ?>

                                <div class="ui warning message">
                                    <p><?php _e( 'You have no matches scheduled.', 'wp-club-manager' ); ?></p>
                                </div>
                            
                            <?php
                            } ?>

                        </div>
                        <div class="extra content">
                            <div class="ui two buttons">
                                <a class="ui basic button" href="<?php echo $new_query; ?>"><?php _e( 'Manage matches', 'wp-club-manager' ); ?></a>
                                <a class="ui basic button" href="<?php echo admin_url( 'post-new.php?post_type=wpcm_match' ); ?>"><?php _e( 'Add new match', 'wp-club-manager' ); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="column">
                <div class="ui fluid card">
                    <div class="content">
                        <div class="header"><?php _e( 'Staff Roster', 'wp-club-manager' ); ?></div>
                    </div>
                    <div class="content">
                        
                        <?php
                        if( $employees ) { ?>

                        <table class="ui compact unstackable table">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Name', 'wp-club-manager' ); ?></th>
                                    <th class="right aligned"><?php _e( 'Actions', 'wp-club-manager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach( $employees as $employee ) { ?>

                                <tr>
                                    <td><a href="<?php echo get_permalink( $employee->ID ); ?>"><?php echo get_player_title( $employee->ID, 'full' ); ?></a></td>
                                    <td class="right aligned"><a class="mini ui basic button" href="<?php echo get_edit_post_link( $employee->ID ); ?>"><?php _e( 'Edit', 'wp-club-manager' ); ?></a></td>
                                </tr>

                                <?php
                                } ?>
                            
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="extra content">
                        <div class="ui two buttons">
                            <a class="ui basic button" href="<?php echo get_edit_post_link( $roster_id ); ?>"><?php _e( 'Manage roster', 'wp-club-manager' ); ?></a>
                        </div>
                    </div>

                    <?php
                    } else { ?>

                        <div class="ui warning message">
                            <p><?php _e( 'You have not created a roster yet.', 'wp-club-manager' ); ?></p>
                        </div>

                    </div>
                    <div class="extra content">
                        <div class="ui two buttons">
                            <a class="ui basic button" href="<?php echo admin_url( 'post-new.php?post_type=wpcm_roster' ); ?>"><?php _e( 'Create new roster', 'wp-club-manager' ); ?></a>
                        </div>
                    </div>
                    <?php
                    } ?>

                </div>
            </div>
        </div>
    
</div>

<div class="ui basic vertical segment">
    <div class="ui stackable two column grid">

        <div class="column">
            <div class="ui fluid card">
                <div class="content">
                    <div class="header"><?php _e( 'League Table', 'wp-club-manager' ); ?></div>
                </div>
                <div class="content">
                    
                    <?php
                    if( $clubs ) { ?>

                        <div class="ui small grey header">
                            <?php echo $comps[0]->name; ?>
                        </div>

                        <table class="ui unstackable compact table">
                            <thead>
                                <tr> 
                                    <th></th>   
                                    <th></th>
                                    <?php foreach( $stats as $stat ) { ?>
                                
                                        <th class="<?php echo $stat; ?>"><?php echo $stats_labels[$stat]; ?></th>
                                
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                
                            <?php
                            $rownum = 0;
                            foreach ( $clubs as $club ) {

                                $club_stats = $club->wpcm_stats; ?>

                                <tr <?php echo ( $club->ID == $default_club ? 'class="positive"' : '' ); ?>>
                                    <td><b><?php echo $club->place; ?></b></td>
                                    <td>
                                        <?php if( $default_club == $club->ID ) {
                                            if ( $team_label ) {
                                                echo $team_label;
                                            } else {
                                                echo $club->post_title;
                                            }
                                        } else {
                                            echo $club->post_title;
                                        } ?>
                                    </td>

                                    <?php foreach( $stats as $stat ) { ?>
                                
                                        <td class="<?php echo $stat; ?>"><?php echo $club_stats[$stat]; ?></td>

                                    <?php } ?>

                                </tr>
                            
                            <?php
                            } ?>
                            
                            </tbody>
                        </table>

                    </div>
                    <div class="extra content">
                        <div class="ui two buttons">
                            <a class="ui basic button" href="<?php echo get_edit_post_link( $table_id ); ?>"><?php _e( 'Manage table', 'wp-club-manager' ); ?></a>
                        </div>
                    </div>

                    <?php
                    } else { ?>

                        <div class="ui warning message">
                            <p><?php _e( 'You have not created a league table yet.', 'wp-club-manager' ); ?></p>
                        </div>
                    </div>
                    <div class="extra content">
                        <div class="ui two buttons">
                            <a class="ui basic button" href="<?php echo admin_url( 'post-new.php?post_type=wpcm_table' ); ?>"><?php _e( 'Create new league table', 'wp-club-manager' ); ?></a>
                        </div>
                    </div>

                    <?php
                    } ?>
            </div>
        </div>
        <div class="column">
            <div class="ui fluid card">
                <div class="content">
                    <div class="header"><?php _e( 'Players Roster', 'wp-club-manager' ); ?></div>
                </div>
                <div class="content">
                    
                    <?php
                    if( $players ) { ?>

                        <table class="ui compact unstackable table">
                            <thead>
                                <tr>
                                    <th><?php _ex( 'No.', 'Squad Number', 'wp-club-manager' ); ?></th>
                                    <th><?php _e( 'Name', 'wp-club-manager' ); ?></th>
                                    <th><?php _e( 'Position', 'wp-club-manager' ); ?></th>
                                    <th class="right aligned"><?php _e( 'Actions', 'wp-club-manager' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach( $players as $player ) { ?>

                                <tr>
                                    <td><?php echo get_post_meta( $player->ID, 'wpcm_number', true ); ?></td>
                                    <td><a href="<?php echo get_permalink( $player->ID ); ?>"><?php echo get_player_title( $player->ID, 'full' ); ?></a></td>
                                    <td><?php echo wpcm_get_player_positions( $player->ID ); ?></td>
                                    <td class="right aligned"><a class="mini ui basic button" href="<?php echo get_edit_post_link( $player->ID ); ?>"><?php _e( 'Edit', 'wp-club-manager' ); ?></a></td>
                                </tr>

                                <?php
                                } ?>
                            
                            </tbody>
                        </table>
                    </div>
                    <div class="extra content">
                        <div class="ui two buttons">
                            <a class="ui basic button" href="<?php echo get_edit_post_link( $roster_id ); ?>"><?php _e( 'Manage roster', 'wp-club-manager' ); ?></a>
                        </div>
                    </div>

                    <?php
                    } else { ?>

                        <div class="ui warning message">
                            <p><?php _e( 'You have not created a roster yet.', 'wp-club-manager' ); ?></p>
                        </div>

                    </div>
                    <div class="extra content">
                        <div class="ui two buttons">
                            <a class="ui basic button" href="<?php echo admin_url( 'post-new.php?post_type=wpcm_roster' ); ?>"><?php _e( 'Create new roster', 'wp-club-manager' ); ?></a>
                        </div>
                    </div>
                    <?php
                    } ?>
            </div>
        </div>

    </div>
</div>