{{-- this HTML is inserted into app.blade.php as content --}}
@extends('app')

@section('content')

{{-- this section is commented out for localhost running, when
live on ResViz, remove the comment symbols on lines 10 & 16 --}}

{{-- USER LOGIN SECURITY --}}
<?php
    require('redis-session.php');
    RedisSession::start();

    if (!isset($_SESSION['HTTP_SHIB_EP_EMAILADDRESS'])) {
        header('Location: https://resviz.ncl.ac.uk/signin?redirect=https://resviz.ncl.ac.uk/wos/index.php');
	die();
    }
?>

{{-- local script --}}
{!! HTML::script('js/script.js') !!}

<div class="row">
	{{-- search form --}}
	{{-- using Illuminate\Html\HtmlServiceProvider package --}}
	{!! Form::open(['url' => 'data', 'id' => 'form']) !!}
		<fieldset>
			<div class="form-group">
				{{-- see http://getbootstrap.com/css/#grid for explanation of Bootstrap grid system --}}
				<div class="col-lg-6 well bs-component">
					{{-- 'journal(s) section of form' --}}
					<div class="journal_fields_wrap">
						{{-- 'journal(s)' section header --}}
						<h4 class="form_title">Journal</h4>
						{{-- buttons above 'journal(s)' input boxes --}}
						<div class="journal_buttons">
							{{-- loads a list of journals on Web of Science --}}
							<a class="btn btn-success" target="_blank" href="http://ip-science.thomsonreuters.com/cgi-bin/jrnlst/jloptions.cgi?PC=D"
							    data-toggle="tooltip-down" title="Search Thomson Reuters for journals">Journal List</a>
							{{-- add extra input field for journals --}}
							{!! Form::button('<span class="glyphicon glyphicon-plus"></span>    Add more fields', ['class' => 'add_journal_field_button btn btn-info']) !!}
						</div> {{-- journal_buttons --}}
						{{-- input box for journal(s) --}}
						<div class="form_field">
							{{-- parameters: textbox(name, default value, options array) --}}
							{!! Form::text('journal1', null, ['class' => 'form-control', 'data-toggle' => 'tooltip-right', 'title' => 'Please enter only one journal per box']) !!}
						</div> {{-- form_field --}}
					</div> {{-- journal_fields_wrap --}}

					{{-- 'keyword(s)' section of form' --}}
					<div class="title_fields_wrap">
						{{-- 'keyword(s)' section header --}}
						<h4 class="form_title">Keyword</h4>
						{{-- buttons above 'keyword(s)' input boxes --}}
						<div class="title_buttons">
							{{-- add extra input field for keywords --}}
							{!! Form::button('<span class="glyphicon glyphicon-plus"></span>    Add more fields', ['class' => 'add_title_field_button btn btn-info']) !!}
						</div> {{-- title_buttons --}}
						{{-- input box for keyword(s) --}}
						<div class="form_field">
							{{-- parameters: textbox(name, default value, options array) --}}
							{!! Form::text('title1', null, ['class' => 'form-control', 'data-toggle' => 'tooltip-right', 'title' => 'Please enter only one title per box']) !!}
						</div> {{-- form_field --}}
					</div> {{-- title_fields_wrap --}}

					{{-- 'time span' section of form; header --}}
					<h4 class="form_title">Time Span</h4></br>
					{{-- 'From:' header --}}
					{!! Form::label('select', 'From:', ['class' => 'col-lg-2 control-label']) !!}
					<div class="col-lg-3">
						{{-- parameters: selectbox(name, [data], default, [options]) --}}
						{{-- data (years) provided by script.js --}}
					    {!! Form::select('timeStart', [], 'Select', ['class' => 'form-control', 'id' => 'select']) !!}
					</div> {{-- col-lg-3 --}}
					{{-- 'To:' header --}}
					{!! Form::label('select', 'To:', ['class' => 'col-lg-2 control-label']) !!}
					<div class="col-lg-3">
						{{-- as other select box above --}}
					    {!! Form::select('timeEnd', [], 'Select', ['class' => 'form-control', 'id' => 'select']) !!}
					</div> {{-- col-lg-3 --}
					<br/><br/>

					{{-- execute search; submit button --}}
					{{-- parameters: button(text on button, [options]) --}}
					{!! Form::button('<strong>Submit</strong><span class="glyphicon glyphicon-transfer"></span>', ['type' => 'submit', 'class' => 'btn btn-primary btn-lg', 'id' => 'submit']) !!}

				</div> {{-- col-lg-6 --}}

				<div class="col-lg-6 well bs-component">
					{{-- bootstrap window --}}
					<div class="modal-dialog">
						<h4>Notes</h4>
						<p>This application is optimised for Chrome.</p>
						<p>In order to get the best results from your search,<br/>enter one or more journals.</p>
						<p>Keywords and time spans are optional but can be<br/>used to refine your search.</p>
					</div> {{-- modal-dialog --}}

				</div> {{-- col-lg-6 --}}

			</div> {{-- form-group --}}
		</fieldset>
	{!! Form::close() !!}
</div> {{-- row --}}

@stop
