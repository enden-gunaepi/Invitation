@extends('invitations.templates.wedding-peach.partials.layout')

@section('content')

    @include('invitations.templates.wedding-peach.partials.splash')

    <!-- Main Content Wrapper (Hidden initially via Alpine logic in layout, but handled by z-index and splash overlay) -->
    <div class="relative w-full pb-20">
        
        @include('invitations.templates.wedding-peach.partials.hero')
        
        @include('invitations.templates.wedding-peach.partials.quote')
        
        @include('invitations.templates.wedding-peach.partials.couple')
        
        @include('invitations.templates.wedding-peach.partials.events')
        
        @include('invitations.templates.wedding-peach.partials.gallery')
        
        @include('invitations.templates.wedding-peach.partials.gifts')
        
        @include('invitations.templates.wedding-peach.partials.rsvp')
        
        @include('invitations.templates.wedding-peach.partials.footer')
        
        @include('invitations.templates.wedding-peach.partials.nav')
        
    </div>

@endsection
