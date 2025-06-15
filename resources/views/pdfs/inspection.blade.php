<!DOCTYPE html>
<html>

<head>
    <title>Export PDF | {{ config('app.name') }}</title>

    <style>
        @page {
            margin: 100px 50px 60px 50px;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            padding: 20px;
        }

        .header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .header img {
            max-height: 60px;
            width: auto;
            max-width: calc(210mm - 25mm - 25mm);
        }

        .footer {
            position: fixed;
            bottom: -50px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 12px;
            max-height: 100px;
        }

        .footer .footer-text {
            margin-bottom: 7px;
            margin-top: 7px;
            max-height: 30px;
            text-align: center;
        }

        .page-number {
            position: fixed;
            bottom: -50px;
            right: 0;
            font-size: 12px;
        }

        .main-title {
            font-size: 24px;
            margin: 0 0 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        tr td:first-child {
            width: 40%;
            font-weight: bold;
        }

        td {
            padding: 3px 0px;
        }

        table.bordered td {
            border: 1px solid #000;
            padding-left: 10px;
        }

        .image {
            max-height: 300px;
            max-width: 300px;
        }

        .multi-image {
            margin-bottom: 10px;
        }

        .multi-image:last-child {
            margin-bottom: 0px;
        }

        .check-cross {
            width: 15px;
            height: 15px;
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ base64File('assets/images/logo-dark.png') }}" />
    </div>

    <div class="footer">
        <div class="footer-text">{{ config('app.name') }} &copy; {{ date('Y') }}</div>
    </div>

    <div class="page-number">
        <span class="page"></span>
    </div>

    <h1 class="main-title">
        {{ implode(' ', [$vehicle->make, $vehicle->model]) }}
        <span style="font-size: 0.6em;">{{ $vehicle->year }}</span>
    </h1>

    <table>
        <tr>
            <td>Fuel Type:</td>
            <td>{{ ucwords($vehicle->fuel_type) }}</td>
        </tr>
        <tr>
            <td>Color:</td>
            <td>{{ $vehicle->color }}</td>
        </tr>
        <tr>
            <td>License Plate:</td>
            <td>{{ $vehicle->license_plate }}</td>
        </tr>
        <tr>
            <td>Milage:</td>
            <td>{{ $vehicle->milage }}</td>
        </tr>
        <tr>
            <td>Registration:</td>
            <td>{{ $vehicle->registration }}</td>
        </tr>
        <tr>
            <td>Created At:</td>
            <td>{{ frontendDateTime($vehicle->crated_at) }}</td>
        </tr>
        <tr>
            <td>Updated At:</td>
            <td>{!! $vehicle->updated_at ? frontendDateTime($vehicle->updated_at) : '<small><i>Never Updated</i></small>' !!}</td>
        </tr>
        <tr>
            <td>Inspection Started At:</td>
            <td>{{ frontendDateTime($inspection->started_at) }}</td>
        </tr>
        <tr>
            <td>Inspection Completed At:</td>
            <td>{{ frontendDateTime($inspection->completed_at) }}</td>
        </tr>
    </table>

    <h1 class="main-title" style="margin-top: 20px;">Inspector:</h1>

    <table>
        <tr>
            <td>Name:</td>
            <td>{{ $vehicle->user->name }}</td>
        </tr>
        <tr>
            <td>Email:</td>
            <td>{{ $vehicle->user->email }}</td>
        </tr>
        <tr>
            <td>Phone:</td>
            <td>{{ $vehicle->user->phone }}</td>
        </tr>
        <tr>
            <td>Address:</td>
            <td>{!! canEmpty($vehicle->user->address) !!}</td>
        </tr>
    </table>

    @foreach ($checklists as $checklist)
        <h1 class="main-title" style="margin-top: 20px;">{{ $checklist->title }}</h1>

        <table class="bordered">
            @foreach ($checklist->checklistItems as $item)
                @php $itemResult = $item->checklistItemResults->first(); @endphp

                <tr>
                    <td>{{ $item->title }}</td>
                    <td>
                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_IMAGE)
                            @if ($itemResult?->value)
                                <img src="{{ base64File($itemResult->value) }}" class="image" />
                            @else
                                <small><i>No Image</i></small>
                            @endif
                        @endif

                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_MULTI_IMAGE)
                            @if (empty($itemResult?->value))
                                <small><i>No Images</i></small>
                            @else
                                @foreach ($itemResult->value as $image)
                                    <img src="{{ base64File($image) }}" class="image multi-image" />
                                @endforeach
                            @endif
                        @endif

                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_VIDEO)
                            @if ($itemResult?->value)
                                <a href="{{ $itemResult->formattedValue }}" target="_blank">
                                    <p>{{ $itemResult->formattedValue }}</p>
                                </a>
                            @else
                                <small><i>No Video</i></small>
                            @endif
                        @endif

                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_TEXT)
                            {!! canEmpty($itemResult?->value) !!}
                        @endif

                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_NUMBER)
                            {!! canEmpty($itemResult?->formattedValue) !!}
                        @endif

                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_BOOLEAN)
                            {{ $itemResult?->value ? 'Yes' : 'No' }}
                        @endif

                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_SELECT)
                            {{ $itemResult?->formattedValue }}
                        @endif

                        @if ($item->item_type === \App\Models\ChecklistItem::ITEM_TYPE_MULTISELECT)
                            @if ($item?->itemOptions?->isNotEmpty())
                                @foreach ($item->itemOptions as $option)
                                    @php
                                        $icon =
                                            $itemResult?->formattedValue && in_array($option->uuid, $itemResult->formattedValue)
                                                ? 'assets/images/check.jpg'
                                                : 'assets/images/cross.jpg';
                                    @endphp

                                    <p>
                                        <img src="{{ base64File($icon) }}" class="check-cross" /> {{ $option->label }}
                                    </p>
                                @endforeach
                            @else
                                <p>No option available for this section.</p>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endforeach
</body>

</html>
