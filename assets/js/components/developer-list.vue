<template>
    <div>
        <v-list  three-line>
            <v-subheader>Developer Progress</v-subheader>
            <template v-for="item in values">
                <v-divider inset></v-divider>
                <v-list-tile avatar v-bind:key="item.name" :title="item.name">
                    <v-list-tile-avatar @click.stop="showPrList(item)">
                        <v-badge left overlap color="green">
                            <span slot="badge">{{ item.total }}</span>
                            <img v-bind:src="item.avatar">
                        </v-badge>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>
                            {{item.name}}
                        </v-list-tile-title>

                        <v-list-tile-sub-title>
                            <v-chip v-if="item.done"  small class="primary white--text">
                                <v-icon>fa-code-fork</v-icon>&nbsp;
                                {{ item.public_repos }} Public Repo
                            </v-chip>
                            <v-chip v-if="item.done"  small class="primary white--text">
                                <v-icon>fa-users</v-icon>&nbsp;
                                {{ item.following }} Following
                            </v-chip>
                            <v-chip v-if="item.done"  small class="primary white--text">
                                <v-icon>fa-user</v-icon>&nbsp;
                                {{ item.followers }} Followers
                            </v-chip>
                        </v-list-tile-sub-title>

                        <v-list-tile-sub-title>
                            <v-progress-linear v-if="!item.done" v-bind:indeterminate="true"></v-progress-linear>
                            <v-progress-linear v-else :value="item.total / 4.0 * 100"></v-progress-linear>
                        </v-list-tile-sub-title>

                    </v-list-tile-content>
                </v-list-tile>
            </template>
        </v-list>

        <v-dialog v-model="dialog" lazy absolute width="600px">
            <v-card>
                <v-card-title>
                    <div class="headline">{{dialogData.name}} - PR List</div>
                </v-card-title>
                <v-card-text>
                    <v-list two-line>
                        <template v-for="(title, url) in dialogData.list">
                            <v-divider></v-divider>
                            <v-list-tile>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        <a :href="url" target="_blank">{{parsePr(url)}}</a>
                                    </v-list-tile-title>
                                    <v-list-tile-sub-title>
                                        {{ title }}
                                    </v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </template>
                    </v-list>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" flat="flat" @click.native="dialog = false">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
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
                dialog: false,
                dialogData: {},
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
            showPrList(user) {
                this.dialogData = user;
                this.dialog = true;
            },
            parsePr(pr) {
                let parts = pr.substr(19).split('/');

                return `${parts[0]}/${parts[1]}#${parts[3]}`;
            },
            fetch(user) {
                fetch('/index.php/api/' + user).then((response) => {
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