<template>
    <div>
        <div  class="progress" :class="{'blue':loaderType=='load'|| loaderType=='save', 'red':loaderType=='delete'}">
            <div class="indeterminate blue lighten-4"></div>
        </div>
    </div>
</template>

<script>
    export default {
        props:['loaderType'],

    }
</script>
