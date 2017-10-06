<template>
    <v-container grid-list-md text-xs-center fluid;>
        <v-layout row wrap>
            <div class="vs">VS</div>
            <v-flex xs6>
                <v-card class="header miway elevation-10">
                    <v-card-text class="px-0">
                        <!--<img src="//cdn.afrihost.com/images/nav/logo.png" width="235" height="55" alt="Afrihost">-->
                    </v-card-text>
                </v-card>
                <br />
                <v-card flat>
                    <v-card-text class="indigo--text">
                        PR Count
                        <div class="counter">60</div>
                    </v-card-text>
                </v-card>
                <br />
                <hr />
                <br />
                <developer-list team="miway"></developer-list>
            </v-flex>
            <v-flex xs6>
                <v-card  class="header afrihost">
                    <v-card-text class="px-0">
                        <!--<img src="//cdn.afrihost.com/images/nav/logo.png" width="235" height="55" alt="Afrihost">-->
                    </v-card-text>
                </v-card>
                <br />
                <v-card flat>
                    <v-card-text class="indigo--text">
                        PR Count
                        <div class="counter">60</div>
                    </v-card-text>
                </v-card>
                <br />
                <hr />
                <br />
                <developer-list team="afrihost"></developer-list>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
    import DeveloperList from './developer-list.vue';

    export default {
        components: {
            'developer-list': DeveloperList
        },
        data() {
            return {
                miway: 90,
                afrihost: 60,
            }
        }
    }
</script>