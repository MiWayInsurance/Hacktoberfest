<template>
    <v-list>
        <v-subheader>Developer Progress</v-subheader>
        <template v-for="item in values">
            <v-divider inset></v-divider>
            <v-list-tile avatar v-bind:key="item.name" :title="item.name">
                <v-list-tile-avatar>
                    <v-badge left overlap color="green">
                        <span slot="badge">{{ item.total }}</span>
                        <img v-bind:src="item.avatar">
                    </v-badge>
                </v-list-tile-avatar>
                <v-list-tile-content>
                    <v-list-tile-title>
                        <v-progress-linear :value="item.total * 4"></v-progress-linear>
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </template>
    </v-list>
</template>

<script>
    import {sortBy, reverse} from 'lodash';

    export default {
        props: {
            items: {
                type: Object,
                required: true
            }
        },
        computed: {
            values: function() {
                return reverse(sortBy(this.items, 'total'));
            }
        }
    }
</script>