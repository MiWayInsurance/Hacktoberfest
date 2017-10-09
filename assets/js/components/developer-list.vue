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
                        <v-progress-linear v-if="!item.done" v-bind:indeterminate="true"></v-progress-linear>
                        <v-progress-linear v-else :value="item.total * 4"></v-progress-linear>
                    </v-list-tile-title>
                </v-list-tile-content>
            </v-list-tile>
        </template>
    </v-list>
</template>

<script>
    import {sortBy, reverse, sum, map, find, extend} from 'lodash';

    export default {
        props: {
            items: {
                type: Array,
                required: true
            },
            team: {
                type: String,
                require: true
            }
        },
        data() {
            return {
                values: this.getValues()
            }
        },
        mounted() {
            this.items.forEach((item) => {
                this.fetch(item);
            })
        },
        methods: {
            getValues() {
                return map(this.items, (name) => {
                    return {name};
                });
            },
            fetch(user) {
                fetch('/api/' + user).then((response) => {
                    response.json().then((result) => {
                        extend(find(this.values, {'name': user}), result, {'done': true});
                        this.$emit('update', sum(map(this.values, 'total')), this.team);
                        this.values = reverse(sortBy(this.values, 'total'));
                    });
                });
            }
        }
    }
</script>