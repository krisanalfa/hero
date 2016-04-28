@extends('layouts.main')

@section('content')
<div id='loading-overlay' class="mdl-color--indigo">
  <div>
    <div class="sk-double-bounce">
      <div class="sk-child sk-double-bounce1 mdl-color--white"></div>
      <div class="sk-child sk-double-bounce2 mdl-color--white"></div>
    </div>

    <i class="mdl-color-text--grey-50">Preparing the awesomeness ...</i>
  </div>
</div>

<!-- Always shows a header, even in smaller screens. -->
<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header">
  <header class="mdl-layout__header">
    <div class="mdl-layout__header-row">
      <!-- Title -->
      <span class="mdl-layout-title">Hero</span>
      <!-- Add spacer, to align navigation to the right -->
      <div class="mdl-layout-spacer"></div>
      <!-- Navigation. We hide it in small screens. -->
      <nav class="mdl-navigation mdl-layout--large-screen-only">
        <a class="mdl-navigation__link" href="javascript:;">Link</a>
      </nav>
    </div>
  </header>
  <div class="mdl-layout__drawer">
    <span class="mdl-layout-title">Hero</span>
    <nav class="mdl-navigation">
      <a class="mdl-navigation__link" href="javascript:;">Link</a>
    </nav>
  </div>
  <main class="demo-layout mdl-layout__content mdl-color--grey-50">
    <div class="demo-page-content page-content">
      <div class="demo-card mdl-card mdl-shadow--2dp mdl-color--indigo">
        <div class="mdl-card__title mdl-card--expand">
          <h2 class="mdl-card__title-text mdl-color-text--grey-50">Welcome to Hero!</h2>
        </div>
        <div class="mdl-card__supporting-text">
          <span class="mdl-color-text--grey-50">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenan convallis.</span>
        </div>
        <div class="mdl-card__actions mdl-card--border">
          <a class="mdl-button mdl-button--colored mdl-js-button mdl-js-ripple-effect mdl-color-text--grey-50">Cool</a>
        </div>
      </div>
    </div>
  </main>
</div>
@stop
