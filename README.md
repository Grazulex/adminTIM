tim
===
insert into fos_user_user_group (user_id, group_id) (select user.id, 2 from user where user.id not in (select fos_user_user_group.user_id from fos_user_user_group))

Merci Thibs

            - 
                position: right
                type: sonata.timeline.block.timeline
                settings: 
                    template: 'AppBundle:Block:timeline.html.twig'
                    max_per_page: 25  
