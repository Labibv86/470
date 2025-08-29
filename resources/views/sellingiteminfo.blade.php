<!DOCTYPE html>
<html>
<head>
    <title>Sell Your Car</title>
{{--    @vite(['resources/css/sellingiteminfo.css'])--}}
    <link href="/css/sellingiteminfo.css" rel="stylesheet">

</head>
<body>

<div class="main">
<form action="{{ route('sellingiteminfo.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

        <div class="first">
            <p class="text">Provide Car Information</p>
        </div>


        <div class="container">

            <div class="boxes">
                <input class="allinputbox" type="text" id="name" name="name" placeholder="Make" value="{{ old('name') }}" required />
                <input class="allinputbox" type="text" id="model" name="model" placeholder="Model" value="{{ old('model') }}" required />
            </div>

            <div class="boxes">
                <select class="allinputbox" id="category" name="category" required>
                    <option value="">Select Car Performance Class</option>
                    <option value='S1 Class' {{ old('category') == 'S1 Class' ? 'selected' : '' }}>S1 Class</option>
                    <option value='A Class' {{ old('category') == 'A Class' ? 'selected' : '' }}>A Class</option>
                    <option value='B Class' {{ old('category') == 'B Class' ? 'selected' : '' }}>B Class</option>
                    <option value='C Class' {{ old('category') == 'C Class' ? 'selected' : '' }}>C Class</option>
                </select>
            </div>

            <div class="boxes">

                <textarea class="allinputbox" id="description" name="description" rows="2" placeholder="Description" required>{{ old('description') }}</textarea>

            </div>

            <div class="boxes">

                <input class="allinputbox" type="number" id="originalPrice" name="originalprice" min="0" placeholder="Original Price" value="{{ old('originalprice') }}" required />


                <input class="allinputbox" type="number" id="askingPrice" name="askingprice" min="0" placeholder="Selling Price" value="{{ old('askingprice') }}" required />
            </div>

            <div class="imageboxes">
                <input class="custom-file-input" type="file" id="itemimage" name="itemimage" accept="image/*" required>
            </div>


            <div class="submitboxes">
                <button class="submit" type="submit" name="submit" >Submit Item</button>
            </div>

        </div>

</form>
    <div class="header">
        <form action="{{ route('sellingiteminfo.backtoseller') }}" method="POST" style="display:inline;">
            @csrf
            <button class="backtorental" type="submit">Cancel</button>
        </form>
    </div>
</div>

</body>
</html>
