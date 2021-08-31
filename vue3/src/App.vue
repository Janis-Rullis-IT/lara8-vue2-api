<template>
  <div>
		<div class="row header">
			<div class="row">
				<div class="col-sm-3 logo">
					<a href="/"><img alt="Home" src="/ui/img/logo-placeholder.jpg"></a>
				</div>
				<div class="col-sm-6"></div>
				<div class="col-sm-3">
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12 link-menu">&nbsp;</div>
			</div>
		</div>

    <div id="app">
        <router-view class="view"></router-view>
    </div>
  </div>
</template>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}

#nav {
  padding: 30px;
}

#nav a {
  font-weight: bold;
  color: #2c3e50;
}

#nav a.router-link-exact-active {
  color: #42b983;
}
</style>
