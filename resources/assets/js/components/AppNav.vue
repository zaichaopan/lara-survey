<template>
<div class="bg-teal">
    <nav class="container mx-auto flex items-center justify-between flex-wrap px-6 py-4">
        <div class="flex items-center flex-no-shrink text-white mr-6">
            <span class="font-semibold text-xl tracking-tight">Lara Survey</span>
        </div>
        <div class="block lg:hidden" @click="hideResponsiveHeader=!hideResponsiveHeader">
            <button class="flex items-center px-3 py-2 border rounded text-teal-lighter border-teal-light hover:text-white hover:border-white">
                <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <title>Menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
                </svg>
            </button>
        </div>
        <div class="w-full block flex-grow lg:flex lg:items-center lg:w-auto" :class="{'hidden':hideResponsiveHeader}">
            <div class="text-sm lg:flex-grow">
                <a href="/surveys" class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">
                    Survyes
                </a>
                <a href="/surveys/create" class="block mt-4 lg:inline-block lg:mt-0 text-teal-lighter hover:text-white mr-4">
                    Create a new survey
                </a>
            </div>
            <div class="w-auto flex text-right mt-4 lg:mt-0">
                <div>
                    <img class="inline-block h-8 w-8 rounded-full" src="https://avatars0.githubusercontent.com/u/4323180?s=460&v=4" alt="avatar">
                </div>

                <div class="flex items-center ml-2">
                    <span class="text-white text-sm mr-1">{{ user.name }}</span>
                    <div>
                        <svg class="fill-current text-white h-4 w-4 block opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M4.516 7.548c.436-.446 1.043-.481 1.576 0L10 11.295l3.908-3.747c.533-.481 1.141-.446 1.574 0 .436.445.408 1.197 0 1.615-.406.418-4.695 4.502-4.695 4.502a1.095 1.095 0 0 1-1.576 0S4.924 9.581 4.516 9.163c-.409-.418-.436-1.17 0-1.615z" />
                        </svg>
                    </div>
                </div>

            </div>
        </div>
    </nav>
</div>
</template>

<script>
export default {
    props: {
        user: {
            type: Object,
            rquired: true
        }
    },
    data() {
        return { hideResponsiveHeader: true };
    }
};
</script>
