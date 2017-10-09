<template>
    <v-container grid-list-xl text-xs-center>
        <v-layout row wrap  v-bind="binding">
            <div class="vs hidden-md-and-down">VS</div>
            <v-flex v-for="(developers, team) in teams" :key="team">
                <v-card class="header elevation-10" :class="team">
                    <v-card-text class="px-0"></v-card-text>
                </v-card>
                <br />
                <v-card flat>
                    <v-card-text class="indigo--text">
                        PR Count
                        <div class="counter">{{ totalPrs[team] }}</div>
                    </v-card-text>
                </v-card>
                <br />
                <hr />
                <br />
                <developer-list :items="developers" @update="total" :team="team"></developer-list>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import Vue from 'vue';
    import DeveloperList from './developer-list.vue';
    import {sum, map} from 'lodash';

    export default {
        components: {
            'developer-list': DeveloperList
        },
        props: ['teams'],
        data() {
            return {
                totalPrs: {},
            }
        },
        methods: {
            total(total, team) {
                Vue.set(this.totalPrs, team, total);
            }
        },
        computed: {
            binding () {
                const binding = {};

                if (this.$vuetify.breakpoint.mdAndDown) {
                    binding.column = true
                }

                return binding
            }
        }
    }
</script>