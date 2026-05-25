# Wedding Template Data Documentation

This document provides a comprehensive guide to all data variables available in the wedding invitation template. Use this reference when creating new wedding templates to understand what data is available from the Laravel backend.

## Main Data Objects

### $invitation
The main invitation object containing all wedding-related data.

#### Basic Information
- **`$invitation->title`** (string)
  - Title of the invitation
  - Example: "The Wedding of John & Jane"
  - Usage: `{{ $invitation->title }}`

- **`$invitation->slug`** (string)
  - URL slug for the invitation
  - Example: "john-jane-wedding"
  - Usage: Used in routes like `route('invitation.show', $invitation->slug)`

#### Couple Information
- **`$invitation->bride_name`** (string)
  - Full name of the bride
  - Example: "Jane Doe"
  - Usage: `{{ $invitation->bride_name ?? 'Mempelai Wanita' }}`

- **`$invitation->groom_name`** (string)
  - Full name of the groom
  - Example: "John Doe"
  - Usage: `{{ $invitation->groom_name ?? 'Mempelai Pria' }}`

- **`$invitation->bride_parent_name`** (string)
  - Name of the bride's parents
  - Example: "Putra dari Bpk. John Doe & Ibu Jane Doe"
  - Usage: `{{ $invitation->bride_parent_name ?? '-' }}`

- **`$invitation->groom_parent_name`** (string)
  - Name of the groom's parents
  - Example: "Putri dari Bpk. Robert Smith & Ibu Mary Smith"
  - Usage: `{{ $invitation->groom_parent_name ?? '-' }}`

- **`$invitation->bride_instagram`** (string, nullable)
  - Instagram URL of the bride
  - Example: "https://instagram.com/jane_doe"
  - Usage: 
  ```php
  @if ($invitation->bride_instagram)
      <a href="{{ $invitation->bride_instagram }}" target="_blank">@instagram</a>
  @endif
  ```

- **`$invitation->groom_instagram`** (string, nullable)
  - Instagram URL of the groom
  - Example: "https://instagram.com/john_doe"
  - Usage: 
  ```php
  @if ($invitation->groom_instagram)
      <a href="{{ $invitation->groom_instagram }}" target="_blank">@instagram</a>
  @endif
  ```

#### Photos
- **`$invitation->cover_photo`** (string, nullable)
  - File path of the cover photo
  - Example: "photos/cover.jpg"
  - Usage: `{{ $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null }}`

- **`$invitation->bride_photo`** (string, nullable)
  - File path of the bride's photo
  - Example: "photos/bride.jpg"
  - Usage: `{{ $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : null }}`

- **`$invitation->groom_photo`** (string, nullable)
  - File path of the groom's photo
  - Example: "photos/groom.jpg"
  - Usage: `{{ $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : null }}`

- **`$invitation->photos`** (Collection)
  - Collection of photo objects for gallery
  - Each photo has:
    - `$photo->file_path` (string) - File path of the photo
    - `$photo->caption` (string, nullable) - Photo caption
  - Usage:
  ```php
  @foreach ($invitation->photos as $photo)
      <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption ?: '' }}">
  @endforeach
  ```

#### Event Details
- **`$invitation->event_date`** (Carbon date, nullable)
  - Date of the wedding event
  - Example: "2024-12-25"
  - Usage:
  ```php
  {{ $invitation->event_date ? $invitation->event_date->translatedFormat('d.m.y') : '-' }}
  {{ $invitation->event_date ? $invitation->event_date->format('Y-m-d') : null }}
  ```

- **`$invitation->event_time`** (string, nullable)
  - Time of the wedding event
  - Example: "08:00:00"
  - Usage: `{{ $invitation->event_time ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i') : '-' }}`

- **`$invitation->opening_text`** (string, nullable)
  - Opening text/quote for the invitation
  - Example: "Dan di antara tanda-tanda kebesaran-Nya..."
  - Usage: `{{ $invitation->opening_text ?: 'Default opening text' }}`

#### Venue Information
- **`$invitation->venue_name`** (string, nullable)
  - Name of the wedding venue
  - Example: "Grand Ballroom Hotel"
  - Usage: `{{ $invitation->venue_name }}`

- **`$invitation->venue_address`** (string, nullable)
  - Address of the wedding venue
  - Example: "Jl. Sudirman No. 123, Jakarta"
  - Usage: `{{ $invitation->venue_address }}`

- **`$invitation->venue_lat`** (float, nullable)
  - Latitude coordinate of the venue
  - Example: -6.2088
  - Usage: Used for Google Maps embedding

- **`$invitation->venue_lng`** (float, nullable)
  - Longitude coordinate of the venue
  - Example: 106.8456
  - Usage: Used for Google Maps embedding

- **`$invitation->google_maps_url`** (string, nullable)
  - Direct Google Maps URL
  - Example: "https://maps.google.com/?q=Grand+Ballroom"
  - Usage: Fallback for maps if coordinates not available

#### Events Collection
- **`$invitation->events`** (Collection)
  - Collection of event objects (timeline events)
  - Each event has:
    - `$event->event_name` (string) - Name of the event
    - `$event->event_date` (string, nullable) - Date of the event
    - `$event->event_time` (string, nullable) - Time of the event
    - `$event->event_description` (string, nullable) - Description of the event
  - Usage:
  ```php
  @foreach ($invitation->events as $event)
      <h3>{{ $event->event_name }}</h3>
      <p>{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : '-' }}</p>
      <p>{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }} WIB</p>
      <p>{{ $event->event_description }}</p>
  @endforeach
  ```

#### RSVP Collection
- **`$invitation->rsvps`** (Collection)
  - Collection of RSVP responses
  - Each RSVP has:
    - `$rsvp->name` (string) - Guest name
    - `$rsvp->pax` (integer) - Number of guests
    - `$rsvp->status` (string) - Status: 'attending', 'not_attending', 'maybe'
    - `$rsvp->message` (string, nullable) - Guest message
  - Usage:
  ```php
  @foreach ($invitation->rsvps as $rsvp)
      <p>{{ $rsvp->name }}</p>
      <p>{{ $rsvp->pax }} pax</p>
      <p>{{ $rsvp->status }}</p>
      @if ($rsvp->message)
          <p>{{ $rsvp->message }}</p>
      @endif
  @endforeach
  ```

#### Love Stories Collection
- **`$invitation->loveStories`** (Collection)
  - Collection of love story entries
  - Usage: `@if ($invitation->loveStories->count())`

---

### $guest
The guest object containing information about the current guest viewing the invitation.

- **`$guest->name`** (string, nullable)
  - Name of the guest
  - Example: "Budi Santoso"
  - Usage: `{{ $guest->name ?? 'Nama Tamu' }}`

- **`$guest->id`** (integer, nullable)
  - ID of the guest
  - Usage: `<input type="hidden" name="guest_id" value="{{ $guest->id }}">`

- **`$guest->getInvitationUrl()`** (string)
  - Method to get the guest's unique invitation URL
  - Usage: `{{ $guest->getInvitationUrl() }}`

---

### Helper Variables

These are commonly computed variables in the template:

- **`$guestName`** (string)
  - Computed: Guest name with fallback
  - Usage: `$guestName = $guest->name ?? 'Nama Tamu'`

- **`$coupleName`** (string)
  - Computed: Combined couple names
  - Usage: 
  ```php
  $coupleName = trim(
      ($invitation->bride_name ?? 'Mempelai Wanita') . ' & ' . ($invitation->groom_name ?? 'Mempelai Pria'),
  );
  ```

- **`$coverImage`** (string|null)
  - Computed: Full URL of cover photo
  - Usage: `$coverImage = $invitation->cover_photo ? asset('storage/' . $invitation->cover_photo) : null`

- **`$slideshowImages`** (array)
  - Computed: Array of image URLs for slideshow
  - Usage:
  ```php
  $slideshowImages = [];
  if ($invitation->cover_photo) {
      $slideshowImages[] = asset('storage/' . $invitation->cover_photo);
  }
  foreach ($invitation->photos as $photo) {
      $slideshowImages[] = asset('storage/' . $photo->file_path);
  }
  $slideshowImages = array_values(array_unique($slideshowImages));
  ```

- **`$groomPhoto`** (string|null)
  - Computed: Full URL of groom photo
  - Usage: `$groomPhoto = $invitation->groom_photo ? asset('storage/' . $invitation->groom_photo) : null`

- **`$bridePhoto`** (string|null)
  - Computed: Full URL of bride photo
  - Usage: `$bridePhoto = $invitation->bride_photo ? asset('storage/' . $invitation->bride_photo) : null`

- **`$eventDateText`** (string)
  - Computed: Formatted event date
  - Usage: `$eventDateText = $invitation->event_date ? $invitation->event_date->translatedFormat('d.m.y') : '-'`

- **`$eventDateIso`** (string|null)
  - Computed: ISO format event date
  - Usage: `$eventDateIso = $invitation->event_date ? $invitation->event_date->format('Y-m-d') : null`

- **`$eventTimeIso`** (string)
  - Computed: ISO format event time
  - Usage: 
  ```php
  $eventTimeIso = $invitation->event_time
      ? \Carbon\Carbon::parse($invitation->event_time)->format('H:i:s')
      : '00:00:00';
  ```

- **`$openingText`** (string)
  - Computed: Opening text with default fallback
  - Usage: 
  ```php
  $openingText = $invitation->opening_text ?:
      'Dan di antara tanda-tanda kebesaran-Nya ialah Dia menciptakan untukmu pasangan hidup dari jenismu sendiri, agar kamu cenderung dan merasa tenteram kepadanya.';
  ```

- **`$mapsEmbed`** (string)
  - Computed: Google Maps embed URL
  - Usage:
  ```php
  $mapsEmbed = $invitation->venue_lat && $invitation->venue_lng
      ? 'https://www.google.com/maps?q=' . $invitation->venue_lat . ',' . $invitation->venue_lng . '&z=15&output=embed'
      : ($invitation->google_maps_url
          ? 'https://www.google.com/maps?output=embed&q=' . urlencode($invitation->google_maps_url)
          : 'https://www.google.com/maps?output=embed&q=' . urlencode(trim((string) ($invitation->venue_name . ' ' . $invitation->venue_address))));
  ```

- **`$mapsUrl`** (string|null)
  - Computed: Google Maps URL for opening in new tab
  - Usage:
  ```php
  $mapsUrl = $invitation->google_maps_url ?:
      ($invitation->venue_lat && $invitation->venue_lng
          ? 'https://www.google.com/maps?q=' . $invitation->venue_lat . ',' . $invitation->venue_lng
          : null);
  ```

---

### Config Variables

- **`config('app.name')`** (string)
  - Application name from Laravel config
  - Example: "Invitation App"
  - Usage: `{{ config('app.name') }}`

---

## Common Usage Patterns

### Displaying Couple Names
```php
<h1>{{ $coupleName }}</h1>
<!-- or -->
<h1>{{ $invitation->bride_name }} & {{ $invitation->groom_name }}</h1>
```

### Displaying Event Date
```php
<p>{{ $eventDateText }}</p>
<!-- or -->
<p>{{ $invitation->event_date ? $invitation->event_date->translatedFormat('l, d F Y') : '-' }}</p>
```

### Displaying Photos
```php
@if ($coverImage)
    <img src="{{ $coverImage }}" alt="Cover">
@endif
```

### Displaying Slideshow
```php
@if (count($slideshowImages) > 0)
    @foreach ($slideshowImages as $index => $imgUrl)
        <img src="{{ $imgUrl }}" class="{{ $index === 0 ? 'opacity-100' : 'opacity-0' }}">
    @endforeach
@endif
```

### Displaying Events Timeline
```php
@foreach ($invitation->events as $event)
    <div>
        <h3>{{ $event->event_name }}</h3>
        <p>{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('l, d F Y') : '-' }}</p>
        <p>{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '-' }} WIB</p>
        @if ($event->event_description)
            <p>{{ $event->event_description }}</p>
        @endif
    </div>
@endforeach
```

### Displaying RSVP List
```php
@foreach ($invitation->rsvps as $rsvp)
    <div>
        <p>{{ $rsvp->name }}</p>
        <p>{{ $rsvp->status }}</p>
        @if ($rsvp->message)
            <p>{{ $rsvp->message }}</p>
        @endif
    </div>
@endforeach
```

### Displaying Gallery
```php
@if ($invitation->photos->count())
    @foreach ($invitation->photos as $photo)
        <img src="{{ asset('storage/' . $photo->file_path) }}" alt="{{ $photo->caption ?: '' }}">
    @endforeach
@endif
```

### Google Maps Embed
```php
<iframe src="{{ $mapsEmbed }}" style="width:100%;height:280px;border:0;" allowfullscreen loading="lazy"></iframe>
```

---

## Form Handling

### RSVP Form
The RSVP form should include these fields:
- `name` (required) - Guest name
- `pax` (required) - Number of guests
- `phone` (optional) - WhatsApp number
- `status` (required) - Attendance status
- `message` (required) - Guest message
- `guest_id` (optional) - Guest ID if available

Example:
```php
<form method="POST" action="{{ route('invitation.rsvp', $invitation->slug) }}">
    @csrf
    <input type="text" name="name" value="{{ $guest->name ?? '' }}" required>
    <input type="number" name="pax" value="1" required>
    <input type="tel" name="phone" placeholder="628123456789">
    <select name="status" required>
        <option value="attending">Hadir</option>
        <option value="not_attending">Tidak Hadir</option>
        <option value="maybe">Masih Ragu</option>
    </select>
    <textarea name="message" required></textarea>
    @if (!empty($guest?->id))
        <input type="hidden" name="guest_id" value="{{ $guest->id }}">
    @endif
    <button type="submit">Kirim</button>
</form>
```

---

## Tips for Creating New Templates

1. **Always use null coalescing operator (`??`)** to provide fallback values
2. **Check if collections have data before looping**: `@if ($invitation->photos->count())`
3. **Use `asset('storage/')` for file paths** to get proper URLs
4. **Use Carbon date formatting** for consistent date display
5. **Use translatedFormat()** for localized date formats
6. **Always include CSRF token** in forms: `@csrf`
7. **Use helper variables** for computed values to keep templates clean
8. **Test with empty data** to ensure templates handle missing data gracefully

---

## Available Routes

- `route('invitation.show', $invitation->slug)` - View invitation
- `route('invitation.rsvp', $invitation->slug)` - Submit RSVP form

---

## Notes

- All file paths are relative to the storage directory and should be prefixed with `asset('storage/')`
- Date fields use Carbon for formatting
- Collections can be empty, always check before iterating
- Guest object may be null for public invitations
- Use proper null checks throughout the template
