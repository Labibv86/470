<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shop Interface</title>
{{--    @vite(['resources/css/ownerinterface.css'])--}}
    <link href="/resources/css/ownerinterface.css" rel="stylesheet">

</head>
<body>

<div class="header">
    <p><strong>Shop Name:</strong> {{ $shop->shopname }}</p>
    <p><strong>Shop ID:</strong> {{ $shop->shopid }}</p>
    <p><strong>Balance:</strong> {{ $shop->points }} BDT</p>
</div>

<div class="main-content">
    <h2>Add Items</h2>

    <div class="add-items-section">

        <form action="{{ route('ownerinterface.additem') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div ><input type="text" name="itemname" placeholder="Make" value="{{ old('itemname') }}" required /></div>
            <div><input type="text" name="itemmodel" placeholder="Model" value="{{ old('itemmodel') }}" required /></div>
            <div>
                <select name="itemcategory" required>
                    <option value="">Select Category</option>
                    <option value="S1 Class" {{ old('itemcategory') == 'S1 Class' ? 'selected' : '' }}>S1 Class</option>
                    <option value="A Class" {{ old('itemcategory') == 'A Class' ? 'selected' : '' }}>A Class</option>
                    <option value="B Class" {{ old('itemcategory') == 'B Class' ? 'selected' : '' }}>B Class</option>
                    <option value="C Class" {{ old('itemcategory') == 'C Class' ? 'selected' : '' }}>C Class</option>
                </select>
            </div>

            <div>
                <select name="itemstatus" required>
                    <option value="">Car's Status</option>
                    <option value="Verified" {{ old('itemstatus') == 'Verified' ? 'selected' : '' }}>Verified</option>
                    <option value="Unofficial" {{ old('itemstatus') == 'Unofficial' ? 'selected' : '' }}>Unofficial</option>
                </select>
            </div>

            <div>
                <select name="itemcondition" required>
                    <option value="">Car's Condition</option>
                    <option value="New" {{ old('itemcondition') == 'New' ? 'selected' : '' }}>New</option>
                    <option value="Used" {{ old('itemcondition') == 'Used' ? 'selected' : '' }}>Used</option>
                </select>
            </div>

            <div>
                <select name="itemgender" required>
                    <option value="">Clutch/Gear</option>
                    <option value="Men" {{ old('itemgender') == 'Men' ? 'selected' : '' }}>Manual</option>
                    <option value="Women" {{ old('itemgender') == 'Women' ? 'selected' : '' }}>Auto</option>
                </select>
            </div>

            <div><textarea name="itemdescription" rows="3" placeholder="Description" required>{{ old('itemdescription') }}</textarea></div>

            <div><input type="number" name="resaleprice" min="0" placeholder="Resale Price" value="{{ old('resaleprice') }}" required /></div>
            <div><input type="number" name="rentalprice" min="0" placeholder="Rental Price" value="{{ old('rentalprice') }}" required /></div>
            <div><input type="number" name="biddingprice" min="0" placeholder="Bidding Start Price" value="{{ old('biddingprice') }}" required /></div>
            <div><input type="number" name="totalcopies" min="0" placeholder="Total Copies" value="{{ old('totalcopies') }}" required /></div>

            <div><input type="file" name="invitemimage" accept="image/*" required /></div>

            <div><button type="submit" name="add">Add to Inventory</button></div>
        </form>
    </div>



    <div class="rental-items-section">
        <h2>Rental Items</h2>
        <div class="item-list">
            @foreach($itemsRental as $item)
            @php
                $imageSrc = $item->itemimage ? asset('storage/' . $item->itemimage) : asset('images/default-item.png');
                $renterId = $rentalItem[$item->itemserial]->renterid ?? 'N/A';

            @endphp
            <div class="item-card">
                <img src="{{ $imageSrc }}" alt="{{ $item->itemname }}" style="width:250px; object-fit:cover;">
                <p>Item Name: {{ $item->itemname }}</p>
                <p>Model: {{ $item->itemmodel }}</p>
                <p>Rental Price: {{ $item->rentalprice }} BDT</p>
                <p class="itemtext">Renter ID: {{ $renterId }}</p>

                <form action="{{ route('ownerinterface.addtorental') }}" method="POST">
                    @csrf
                    <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">
                    <input type="hidden" name="shop_id" value="{{ $item->shopid }}">
                    <input type="hidden" name="rental_price" value="{{ $item->rentalprice }}">
                </form>
            </div>
            @endforeach
        </div>
    </div>


    <div class="resale-items-section">
        <h2>Resale Items</h2>
        <div class="item-list">
            @foreach($itemsResale as $item)
                @php
                    $imageSrc = $item->itemimage ? asset('storage/' . $item->itemimage) : asset('images/default-item.png');
                    $currentBidder = $resaleItemsWithBidder[$item->itemserial]->lastbidderid ?? 'N/A';
                    $currentBid = $resaleItemsWithBidder[$item->itemserial]->currentbid ?? 'N/A';
                    $resaleSl = $resaleItemsWithBidder[$item->itemserial]->resaleserial;
                @endphp
                <div class="item-card">
                    <img src="{{ $imageSrc }}" alt="{{ $item->itemname }}" style="width:250px;  object-fit:cover;">
                    <p>Item Name: {{ $item->itemname }}</p>
                    <p>Model: {{ $item->itemmodel }}</p>
                    <p>Resale Price: {{ $item->resaleprice }}</p>
                    <p>Current Bid: {{ $currentBid }}</p>
                    <p>Current Bidder: {{ $currentBidder }}</p>

                    <form action="{{ route('ownerinterface.stopbidding') }}" method="POST">
                        @csrf

                        <input type="hidden" name="resale_item_serial" value="{{ $resaleSl }}">

                        <button type="submit" class="sellreqbutton" name="stopbidding">Stop Bidding</button>
                    </form>

                </div>
            @endforeach
        </div>
    </div>


    {{--    <!-- Sell Requests Section -->--}}
{{--    <!-- Sell Requests Section -->--}}
    <div class="sell-requests-section">
        <h2>Sell Requests</h2>

        <div class="item-list">
            @forelse($sellRequests as $request)
                @php
                    $imageSrc = $request->itemimage
                        ? asset('storage/' . $request->itemimage)
                        : asset('images/default-item.png');
                @endphp

                <div class="item-card">
                    <img src="{{ $imageSrc }}" alt="{{ $request->itemname }}" style="width:150px; height:150px; object-fit:cover;">

                    <p><strong>Name:</strong> {{ $request->itemname }}</p>
                    <p><strong>Model:</strong> {{ $request->itemmodel }}</p>
                    <p><strong>Original Price:</strong> {{ $request->originalprice }}</p>
                    <p><strong>Asking Price:</strong> {{ $request->askingprice }}</p>

                    <div class="accrejbutton">


                        <form action="{{ route('ownerinterface.accept') }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                            @csrf
                            <input type="hidden" name="sell_item_serial" value="{{ $request->serial }}">
                            <button type="submit" name="action" value="accept" class="sellreqbutton"> Accept</button>
                        </form>


                        <form action="{{ route('ownerinterface.reject') }}" method="POST" enctype="multipart/form-data" style="display:inline;">
                            @csrf
                            <input type="hidden" name="sell_item_serial" value="{{ $request->serial }}">
                            <button type="submit" name="action" value="reject" class="sellreqbutton"> Reject</button>
                        </form>
                    </div>
                </div>
            @empty

            @endforelse
        </div>
    </div>



<div class="inventory-section">
    <h2>Inventory</h2>
    <div class="item-list">
        @foreach($itemsInventory as $item)
            @php
                $imageSrc = $item->itemimage ? asset('storage/' . $item->itemimage) : asset('images/default-item.png');
                $customerInfo = $itemCustomerInfo[$item->itemserial] ?? null;
            @endphp

            <div class="item-card">
                <img src="{{ $imageSrc }}" alt="{{ $item->itemname }}" style="width:250px; height:150px; object-fit:cover;">
                <p><strong>Car Make:</strong> {{ $item->itemname }}</p>
                <p><strong>Car Model:</strong> {{ $item->itemmodel }}</p>
                <p><strong>Resale Price:</strong> {{ $item->resaleprice }}</p>
                <p><strong>Rental Price:</strong> {{ $item->rentalprice }}</p>
                <p><strong>Bidding Price:</strong> {{ $item->biddingprice }}</p>
                <p><strong>Car Serial:</strong> {{ $item->itemserial }}</p>
                <p><strong>Status:</strong> {{ $item->itemuse }}</p>






                <form action="{{ route('ownerinterface.addtoresale') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">
                    <input type="hidden" name="resale_price" value="{{ $item->resaleprice }}">

                    <button type="submit" class="sellreqbutton">Add to Resale</button>
                </form>


                <form action="{{ route('ownerinterface.addtorental') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">
                    <input type="hidden" name="rental_price" value="{{ $item->rentalprice }}">
                    <button type="submit" class="sellreqbutton">Add to Rental</button>
                </form>

                <form action="{{ route('ownerinterface.backtoshop') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">
                    <button type="submit" class="sellreqbutton">Return</button>
                </form>


                <form action="{{ route('ownerinterface.drop') }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to drop this item?');">
                    @csrf
                    <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">
                    <button type="submit" class="sellreqbutton">Drop</button>
                </form>

                <div class="customer-info-container">
                    <button class="sellreqbutton">Customer Info</button>
                    <div class="customer-info-tooltip">
                        @if($customerInfo)
                            <p><strong>Name:</strong> {{ $customerInfo->firstname }} {{ $customerInfo->lastname }}</p>
                            <p><strong>Email:</strong> {{ $customerInfo->email }}</p>
                            <p><strong>Phone:</strong> {{ $customerInfo->phone }}</p>
                            <p><strong>Address:</strong> {{ $customerInfo->address }}</p>
                        @else
                            <p>N/A - Not currently rented or sold</p>
                        @endif
                    </div>
                </div>


                <div class="edit-section" style="margin-top:10px;">
                    <button type="button" class="toggle-edit-btn">Edit</button>

                    <form action="{{ route('ownerinterface.edit') }}" method="POST" enctype="multipart/form-data" style="display:none;" class="edit-form-fields" id="edit-form-{{ $item->itemserial }}">
                        @csrf
                        <input type="hidden" name="item_serial" value="{{ $item->itemserial }}">

                        <label for="edititemname-{{ $item->itemserial }}">Item's Name</label>
                        <input type="text" id="edititemname-{{ $item->itemserial }}" name="edititemname" value="{{ old('edititemname', $item->itemname) }}" placeholder="Item's Name">

                        <label for="edititemmodel-{{ $item->itemserial }}">Item's Model</label>
                        <input type="text" id="edititemmodel-{{ $item->itemserial }}" name="edititemmodel" value="{{ old('edititemmodel', $item->itemmodel) }}" placeholder="Item's Model">

                        <label for="edititemcategory-{{ $item->itemserial }}">Item's Category</label>
                        <select id="edititemcategory-{{ $item->itemserial }}" name="edititemcategory">
                            <option value="">Select Category</option>
                            <option value="S1 Class" {{ old('edititemcategory', $item->itemcategory) == 'S1 Class' ? 'selected' : '' }}>S1 Class</option>
                            <option value="A Class" {{ old('edititemcategory', $item->itemcategory) == 'A Class' ? 'selected' : '' }}>A Class</option>
                            <option value="B Class" {{ old('edititemcategory', $item->itemcategory) == 'B Class' ? 'selected' : '' }}>B Class</option>
                            <option value="C Class" {{ old('edititemcategory', $item->itemcategory) == 'C Class' ? 'selected' : '' }}>C Class</option>
                        </select>

                        <label for="edititemstatus-{{ $item->itemserial }}">Item Status</label>
                        <select id="edititemstatus-{{ $item->itemserial }}" name="edititemstatus">
                            <option value="">Select Status</option>
                            <option value="Verified" {{ old('edititemstatus', $item->itemstatus) == 'Verified' ? 'selected' : '' }}>Verified</option>
                            <option value="Unofficial" {{ old('edititemstatus', $item->itemstatus) == 'Unofficial' ? 'selected' : '' }}>Unofficial</option>
                        </select>

                        <label for="edititemcondition-{{ $item->itemserial }}">Item Condition</label>
                        <select id="edititemcondition-{{ $item->itemserial }}" name="edititemcondition">
                            <option value="">Select Condition</option>
                            <option value="New" {{ old('edititemcondition', $item->itemcondition) == 'New' ? 'selected' : '' }}>New</option>
                            <option value="Used" {{ old('edititemcondition', $item->itemcondition) == 'Used' ? 'selected' : '' }}>Used</option>
                        </select>

                        <label for="edititemgender-{{ $item->itemserial }}">Clutch/Gear</label>
                        <select id="edititemgender-{{ $item->itemserial }}" name="edititemgender">
                            <option value="">Select Gear Type</option>
                            <option value="Men" {{ old('edititemgender', $item->itemgender) == 'Men' ? 'selected' : '' }}>Manual</option>
                            <option value="Women" {{ old('edititemgender', $item->itemgender) == 'Women' ? 'selected' : '' }}>Auto</option>
                        </select>

                        <label for="edititemdescription-{{ $item->itemserial }}">Description</label>
                        <textarea id="edititemdescription-{{ $item->itemserial }}" name="edititemdescription" rows="3" placeholder="Description">{{ old('edititemdescription', $item->itemdescription) }}</textarea>

                        <label for="editresaleprice-{{ $item->itemserial }}">Resale Price</label>
                        <input type="number" id="editresaleprice-{{ $item->itemserial }}" name="editresaleprice" min="0" value="{{ old('editresaleprice', $item->resaleprice) }}" placeholder="Resale Price">

                        <label for="editrentalprice-{{ $item->itemserial }}">Rental Price</label>
                        <input type="number" id="editrentalprice-{{ $item->itemserial }}" name="editrentalprice" min="0" value="{{ old('editrentalprice', $item->rentalprice) }}" placeholder="Rental Price">

                        <label for="editbiddingprice-{{ $item->itemserial }}">Bidding Start Price</label>
                        <input type="number" id="editbiddingprice-{{ $item->itemserial }}" name="editbiddingprice" min="0" value="{{ old('editbiddingprice', $item->biddingprice) }}" placeholder="Bidding Start Price">

                        <label for="edittotalcopies-{{ $item->itemserial }}">Total Copies</label>
                        <input type="number" id="edittotalcopies-{{ $item->itemserial }}" name="edittotalcopies" min="0" value="{{ old('edittotalcopies', $item->totalcopies) }}" placeholder="Total Copies">

                        <label for="edititemimage-{{ $item->itemserial }}">Item Image</label>
                        <input type="file" id="edititemimage-{{ $item->itemserial }}" name="edititemimage" accept="image/*">

                        <button type="submit" class="sellreqbutton" style="margin-top:10px;">Save Changes</button>
                    </form>
                </div>

            </div>
        @endforeach
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const parent = button.parentElement;
            const editForm = parent.querySelector('.edit-form-fields');
            if (editForm.style.display === 'none' || editForm.style.display === '') {
                editForm.style.display = 'block';
            } else {
                editForm.style.display = 'none';
            }
        });
    });
</script>







<div class="footer">
    <form action="{{ route('ownerinterface.logout') }}" method="POST" style="display:inline;">
        @csrf
        <button class="logout" type="submit" name="logout">Logout</button>
    </form>

</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

</body>
</html>
