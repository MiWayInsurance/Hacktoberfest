<template>
    <v-container grid-list-md text-xs-center fluid;>
        <v-layout row wrap>
            <div class="vs">VS</div>
            <v-flex xs6 v-for="(developers, team) in data">
                <v-card class="header elevation-10" :class="team">
                    <v-card-text class="px-0"></v-card-text>
                </v-card>
                <br />
                <v-card flat>
                    <v-card-text class="indigo--text">
                        PR Count
                        <div class="counter" v-html="total(developers)"></div>
                    </v-card-text>
                </v-card>
                <br />
                <hr />
                <br />
                <developer-list :items="developers"></developer-list>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import DeveloperList from './developer-list.vue';
    import {sum, map} from 'lodash';

    export default {
        components: {
            'developer-list': DeveloperList
        },
        props: ['data'],
        methods: {
            total(items) {
                return sum(map(items, 'total'));
            }
        }
    }
</script>