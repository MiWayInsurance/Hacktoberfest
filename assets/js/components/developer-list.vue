<template>
    <v-list>
        <v-subheader>Developer Progress</v-subheader>
        <template v-for="item in items">
            <v-divider inset></v-divider>
            <v-list-tile avatar v-bind:key="item.title" @click="" download>
                <v-list-tile-avatar>
                    <img v-bind:src="item.avatar">
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        <v-progress-linear value="12"></v-progress-linear>
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </template>
    </v-list>
</template>

<script>
    export default {
        props: {
            team: {
                type: String,
                required: true
            }
        },
        computed: {
            items: function() {
                return this.developers[this.team];
            },
        },
        data() {
            return {
                developers: {
                    miway: [
                        //{ header: 'Today' },
                        { avatar: '/static/doc-images/lists/1.jpg', title: 'Brunch this weekend?', subtitle: "<span class='grey--text text--darken-2'>Ali Connors</span> — I'll be in your neighborhood doing errands this weekend. Do you want to hang out?" },
                        //{ divider: true, inset: true },
                        { avatar: '/static/doc-images/lists/2.jpg', title: 'Summer BBQ <span class="grey--text text--lighten-1">4</span>', subtitle: "<span class='grey--text text--darken-2'>to Alex, Scott, Jennifer</span> — Wish I could come, but I'm out of town this weekend." },
                        //{ divider: true, inset: true },
                        { avatar: '/static/doc-images/lists/3.jpg', title: 'Oui oui', subtitle: "<span class='grey--text text--darken-2'>Sandra Adams</span> — Do you have Paris recommendations? Have you ever been?" }
                    ],
                    afrihost: [
                        //{ header: 'Today' },
                        { avatar: '/static/doc-images/lists/1.jpg', title: 'Brunch this weekend?', subtitle: "<span class='grey--text text--darken-2'>Ali Connors</span> — I'll be in your neighborhood doing errands this weekend. Do you want to hang out?" },
                        //{ divider: true, inset: true },
                        { avatar: '/static/doc-images/lists/2.jpg', title: 'Summer BBQ <span class="grey--text text--lighten-1">4</span>', subtitle: "<span class='grey--text text--darken-2'>to Alex, Scott, Jennifer</span> — Wish I could come, but I'm out of town this weekend." },
                        //{ divider: true, inset: true },
                        { avatar: '/static/doc-images/lists/3.jpg', title: 'Oui oui', subtitle: "<span class='grey--text text--darken-2'>Sandra Adams</span> — Do you have Paris recommendations? Have you ever been?" }
                    ]
                },

            }
        }
    }
</script>