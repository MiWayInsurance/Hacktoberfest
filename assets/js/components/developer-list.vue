<template>
    <div>
        <v-list three-line :subheader="true">
            <v-subheader>Developer Progress</v-subheader>
            <template v-for="item in users">
                <v-divider inset></v-divider>
                <v-list-tile avatar :key="item.user ? item.user.name : item.name">
                    <v-list-tile-avatar @click.stop="showPrList(item)" :class="'cursor-pointer'">
                        <v-badge left overlap color="green" v-if="item.done">
                            <span slot="badge">{{ item.total }}</span>
                            <img :src="item.user.avatar">
                        </v-badge>
                    </v-list-tile-avatar>
                    <v-list-tile-content>
                        <v-list-tile-title>
                            <span v-if="item.user" :html="item.user.name"></span>
                            <span v-else :html="item.user"></span>

                            <v-chip v-if="item.done" small outline class="grey">
                                <v-avatar class="grey darken-2">{{ item.user.followers }}</v-avatar>
                                Followers &nbsp;
                                <v-icon>fa-user</v-icon>&nbsp;
                            </v-chip>
                            <v-chip v-if="item.done" small outline class="grey">
                                <v-avatar class="grey darken-2">{{ item.user.following }}</v-avatar>
                                Following &nbsp;
                                <v-icon>fa-users</v-icon>&nbsp;
                            </v-chip>
                            <v-chip v-if="item.done" small outline class="grey">
                                <v-avatar class="grey darken-2">{{ item.user.public_repos }}</v-avatar>
                                Public Repos &nbsp;
                                <v-icon>fa-code-fork</v-icon> &nbsp;
                            </v-chip>

                        </v-list-tile-title>
                        <v-list-tile-sub-title>
                            <v-progress-linear v-if="!item.done" :indeterminate="true"></v-progress-linear>
                            <v-progress-linear v-else :value="(item.max) ? 100 : item.progress" :color="item.max ? 'warning' : 'primary'"></v-progress-linear>
                        </v-list-tile-sub-title>

                    </v-list-tile-content>
                </v-list-tile>
            </template>
        </v-list>

        <v-dialog v-model="dialog" lazy absolute width="600px">
            <v-card>
                <v-card-title>
                    <div class="headline">
                        <a :href="dialogData.user.profile" :title="'View GitHub Profile'" :target="'_blank'">
                            <v-avatar :size="'40px'">
                                <img :src="dialogData.user.avatar">
                            </v-avatar>
                        </a>
                        {{dialogData.user.name}}
                    </div>
                </v-card-title>
                <v-card-text>
                    <v-list two-line avatar>
                        <v-subheader>PR List</v-subheader>
                        <template v-for="pr in dialogData.list">
                            <v-divider></v-divider>
                            <v-list-tile>
                                <v-list-tile-action>
                                    <v-icon x-large :color="prColor(pr)">fa-code-fork</v-icon>
                                </v-list-tile-action>
                                <v-list-tile-content>
                                    <v-list-tile-title>
                                        <a :href="pr.url" target="_blank">{{parsePr(pr.url)}}</a>
                                    </v-list-tile-title>
                                    <v-list-tile-sub-title>
                                        {{ pr.title }}
                                    </v-list-tile-sub-title>
                                </v-list-tile-content>
                            </v-list-tile>
                        </template>
                        <template v-if="dialogData.list && dialogData.list.length < 1">
                            <v-divider></v-divider>
                            <v-list-tile>
                                <v-list-tile-title>No pull requests</v-list-tile-title>
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
    import {sortBy, reverse, sum, map, find, maxBy, countBy, extend} from 'lodash';

    export default {
        props: {
            userList: {
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
                dialogData: {user: {}},
                users: this.getUsers(),
                max: 0,
            }
        },
        mounted() {
            this.userList.forEach((username) => {
                this.fetch(username);
            });
        },
        watch: {
            users() {
                if (countBy(this.users, 'done')[undefined] === undefined) {
                    let user = maxBy(this.users, 'total');
                    this.max = user.total;
                    user.max = true;
                }
            },
            max() {
                this.users.forEach((user) => {
                    user.progress = (user.total / this.max) * 100;
                });
            },
        },
        methods: {
            prColor(pr) {
                switch (pr.status) {
                    case 'merged':
                        return 'purple';
                    case 'closed':
                        return 'red';
                    case 'open':
                    default:
                        return 'green';
                }
            },
            getUsers() {
                return map(this.userList, (name) => {
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
                        extend(find(this.users, {'name': user}), result, {'done': true, 'progress': result.total});
                        this.$emit('update', sum(map(this.users, 'total')), this.team);
                        this.users = reverse(sortBy(this.users, 'total'));
                    });
                });
            }
        }
    }
</script>