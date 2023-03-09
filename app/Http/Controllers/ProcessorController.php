<?php

namespace App\Http\Controllers;

use App\Mail\DeliveryNo;
use App\Mail\GeneratedPO;
use App\Mail\GmailNotification;
use App\Mail\PreparePO;
use App\Mail\ReportItemm;
use App\Mail\Requestor;
use App\Mail\Requestor_PO;
use App\Mail\SendCanvass;
use App\Mail\Transmital;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Building;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\SupplierItem;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\Canvass;
use App\Models\CanvassItem;
use App\Models\PurchaseRequestItem;
use App\Models\PurchaseRequestHistory;
use App\Models\PurchaseOrder;
use App\Models\Delivery;
use App\Models\Forwarded;
use App\Models\DeniedRequest;
use App\Models\DelFwd;
use App\Models\Order;
use App\Models\Report;
use App\Models\ReportItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Exception;

class ProcessorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['role:Processor']);
    }
    public function index()
    {
        $id = Auth::user()->id;
        // dd($id);
        $item = Item::count();
        $supplier = Supplier::count();
        $canvass_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'For Canvassing')->get()->count();
        $my_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->where('user_id', '=', $id)
            ->whereNOTIn('purchase_request_histories.action', ['Approved', 'Request Denied', 'Request on Hold'])
            ->count();
        $purchaseorder = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->where('user_id', '=', $id)->count();
        $toreceived = Delivery::join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
            )
            ->where('purchase_requests.user_id', '=', $id)
            ->whereIn('del_fwds.status',  ['With Delivery No.', 'For Approval', 'Approved'])
            // ->where('del_fwds.status', '=', 'Approved')
            ->groupby(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
            )
            ->get()
            ->count();
        $approved_prr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Approved')
            ->where('purchase_requests.w_PO', '=', null)
            ->get()
            ->count();
        $approved_po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approved')
            ->get()
            ->count();
        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->select(
                // '*'
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'del_fwds.status'
            )
            ->groupBy(
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'del_fwds.status'
            )
            ->where('del_fwds.status', '=', 'With Delivery No.')
            ->get()
            ->count();
        $userr = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.zipcode',
            )
            ->where('staff.id', '=', $id)
            ->first();
        $rc = Report::count();

        // dd($toreceived);
        return view('processor.dashboard', compact('rc', 'userr', 'delivery', 'approved_po', 'approved_prr', 'toreceived', 'purchaseorder', 'item', 'supplier', 'canvass_pr', 'my_pr'));
    }
    public function supplier_items()
    {
        $supplier = Supplier::where('suppliers.isActive', '=', null)->get();
        $item = Item::where('items.isActive', '=', null)->get();
        $supplieritem = SupplierItem::join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'supplier_items.id',
                'items.item_desc',
                'suppliers.business_name',
                'suppliers.contact_person',
                'suppliers.contact_no',
                'suppliers.email',
                'suppliers.business_add',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
            )->get();
        // dd($supplieritem);
        return view('manage_supplier_items.supplier_items', compact('item', 'supplier', 'supplieritem'));
    }
    public function addsupplier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|max:255',
            'contact_person' => 'required|max:255',
            'contact_no' => 'required|max:255',
            'email' => 'required|max:255',
            'business_add' => 'required|max:255',
        ]);
        $input = json_encode($request->input('business_name'));
        $input2 = json_encode($request->input('contact_person'));
        $input3 = json_encode($request->input('contact_no'));
        $input4 = json_encode($request->input('email'));
        $input5 = json_encode($request->input('business_add'));
        $arr = json_decode($input);
        $arr2 = json_decode($input2);
        $arr3 = json_decode($input3);
        $arr4 = json_decode($input4);
        $arr5 = json_decode($input5);

        // dd($arr);
        for ($i = 0; $i < count($request->input('business_name')); $i++) {
            if ($arr[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'field is required.'
                ], 400);
            }
            if ($arr2[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'field is required.'
                ], 400);
            }
            if ($arr3[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'field is required.'
                ], 400);
            }
            if ($arr4[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'field is required.'
                ], 400);
            }
            if ($arr5[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'field is required.'
                ], 400);
            }
        }
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            // $supplier = new Supplier;
            // $supplier->business_name = $request->input('business_name');
            // $supplier->contact_person = $request->input('contact_person');
            // $supplier->contact_no = $request->input('contact_no');
            // $supplier->email = $request->input('email');
            // $supplier->business_add = $request->input('business_add');
            // $supplier->save();

            $bn = $request->input('business_name');
            $cp = $request->input('contact_person');
            $cn = $request->input('contact_no');
            $email = $request->input('email');
            $ba =  $request->input('business_add');

            for ($i = 0; $i < count($bn); $i++) {
                $data = [
                    'business_name' => $bn[$i],
                    'contact_person' => $cp[$i],
                    'contact_no' => $cn[$i],
                    'email' => $email[$i],
                    'business_add' => $ba[$i],
                ];
                // dd($data);
                DB::table('suppliers')->insert($data);
            }
            return response()->json([
                'success' => 'building added successfully'
            ]);
        }
    }
    public function additem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_desc' => 'required|max:255|unique:items',
        ]);
        $input = json_encode($request->input('item_desc'));
        $arr = json_decode($input);
        for ($i = 0; $i < count($request->input('item_desc')); $i++) {
            if (!isset($arr[$i])) {
                session()->flash('error', 'The item desc is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'The item desc is required.'
                ], 400);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {

            $item = $request->input('item_desc');
            for ($i = 0; $i < count($item); $i++) {
                $data = [
                    'item_desc' => ucwords($item[$i]),
                ];
                // dd($data);
                DB::table('items')->insert($data);
            }
            return response()->json([
                'success' => 'item added successfully'
            ]);
        }
    }
    public function addsupplieritem(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|max:255',
            'brand' => 'required|max:255',
            'unit' => 'required|max:255',
            'offered_price' => 'required|max:255',
            'supplier_id' => 'required|max:255',
        ]);
        $input = json_encode($request->input('item_id'));
        $input2 = json_encode($request->input('brand'));
        $input3 = json_encode($request->input('unit'));
        $input4 = json_encode($request->input('offered_price'));
        $input5 = json_encode($request->input('supplier_id'));
        $arr = json_decode($input);
        $arr2 = json_decode($input2);
        $arr3 = json_decode($input3);
        $arr4 = json_decode($input4);
        $arr5 = json_decode($input5);

        // dd($arr);
        for ($i = 0; $i < count($request->input('item_id')); $i++) {
            if ($arr[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'field is required.'
                ], 400);
            }
            if ($arr2[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'field is required.'
                ], 400);
            }
            if ($arr3[$i] == null) {
                session()->flash('error', 'field is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'The item desc is required.'
                ], 400);
            }
            if ($arr4[$i] == null) {
                session()->flash('error', 'The item desc is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'The item desc is required.'
                ], 400);
            }
            if ($arr5[$i] == null) {
                session()->flash('error', 'The item desc is required.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'The item desc is required.'
                ], 400);
            }
        }

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $ii = $request->input('item_id');
            $brand = $request->input('brand');
            $unit = $request->input('unit');
            $of = $request->input('offered_price');
            $si =  $request->input('supplier_id');

            for ($i = 0; $i < count($ii); $i++) {
                $data = [
                    'item_id' => $ii[$i],
                    'brand' => $brand[$i],
                    'unit' => $unit[$i],
                    'offered_price' => $of[$i],
                    'supplier_id' => $si[$i],
                ];
                // dd($data);
                DB::table('supplier_items')->insert($data);
            }
            return response()->json([
                'success' => 'item added successfully'
            ]);
        }
    }
    public function updatesupplier(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'up_business_name' => 'required|max:255',
            'up_contact_person' => 'required|max:255',
            'up_contact_no' => 'required|max:255',
            'up_email' => 'required|max:255',
            'up_business_add' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $supplier = Supplier::find($id);
            $supplier->business_name = $request->input('up_business_name');
            $supplier->contact_person = $request->input('up_contact_person');
            $supplier->contact_no = $request->input('up_contact_no');
            $supplier->email = $request->input('up_email');
            $supplier->business_add = $request->input('up_business_add');

            $supplier->save();
            return response()->json([
                'success' => 'supplier updated successfully'
            ]);
        }
    }
    public function updateitem(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'up_item_desc' => 'required|max:255',
            // 'up_brand' => 'required|max:255',
            // 'up_unit' => 'required|max:255',
            // 'up_price' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $item = Item::find($id);
            $item->item_desc = $request->input('up_item_desc');
            // $item->brand = $request->input('up_brand');
            // $item->unit = $request->input('up_unit');
            // $item->price = $request->input('up_price');
            if (!empty($request->supplier_id)) {
                $item->supplier_id = $request->input('up_supplier_id');;
            }

            $item->save();
            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function updatesupplieritem(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'item_id' => 'required|max:255',
            'brand' => 'required|max:255',
            'unit' => 'required|max:255',
            'offered_price' => 'required|max:255',
            // 'supplier_id' => 'required|max:255',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $item = SupplierItem::find($id);
            // $item->item_desc = $request->input('up_item_desc');
            $item->brand = $request->input('brand');
            $item->unit = $request->input('unit');
            $item->offered_price = $request->input('offered_price');
            if (!empty($request->supplier_id)) {
                $item->supplier_id = $request->input('supplier_id');;
            }

            $item->save();
            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function deletesupplieritem($id)
    {
        $supplier = SupplierItem::find($id);
        $supplier->delete();
        return response()->json([
            'success' => 'supplier deleted successfully'
        ]);
    }
    public function deletesupplier($id)
    {
        $supplier = Supplier::find($id);
        $supplier->isActive = "1";
        $supplier->update();
        return response()->json([
            'success' => 'supplier deleted successfully'
        ]);
    }
    public function deleteitem($id)
    {
        $item = Item::find($id);
        $item->isActive = "1";
        $item->update();
        return response()->json([
            'success' => 'item deleted successfully'
        ]);
    }
    public function profile()
    {
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select('roles.display_name', 'roles.id')
            ->where('users.id', '=', $id)
            ->first();
        $userr = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                'staff.id',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'staff.sex',
                'staff.barangay',
                'staff.municipality',
                'staff.city',
                'staff.province',
                'staff.zipcode',
                'users.position',
                'staff.contact_no',
                'departments.Dept_name',
                'departments.id'
            )
            ->where('staff.id', '=', $id)
            ->first();
        $department = Department::all();
        return view('manage_profile.profile', compact('department', 'user', 'userr'));
    }
    public function updateprofile(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|max:255',
            'mname' => 'required|max:255',
            'lname' => 'required|max:255',
            'sex' => 'required|max:255',
            'contact_no' => 'required|digits:10',
            'barangay' => 'required|max:255',
            'city' => 'required|max:255',
            'municipality' => 'required|max:255',
            'province' => 'required|max:255',
            'zipcode' => 'required|max:255',
            // 'position' => 'required|max:255',
            // 'department_id' => 'required|max:255',
            'email' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $users = User::find($id);
            if (!empty([$request->fname, $request->email])) {
                $users->name = $request->input('fname');
                $users->email = $request->input('email');
            } else {
            }
            $staff = Staff::find($id);
            if ($staff === null) {
                $staff = new Staff;
                $staff->id = $request->input('profile_id');
            } else {
            }
            if (!empty([
                $request->fname,
                $request->mname,
                $request->lname,
                $request->sex,
                $request->contact_no,
                // $request->email,
                $request->barangay,
                $request->municipality,
                $request->city,
                $request->province,
                $request->zipcode,
                // $request->position,
                $request->department_id,
            ])) {
                $staff->fname = $request->input('fname');
                $staff->mname = $request->input('mname');
                $staff->lname = $request->input('lname');
                $staff->sex = $request->input('sex');
                // $staff->email = $request->input('email');
                $staff->contact_no = $request->input('contact_no');
                $staff->barangay = $request->input('barangay');
                $staff->municipality = $request->input('municipality');
                $staff->city = $request->input('city');
                $staff->province = $request->input('province');
                // $staff->position = $request->input('position');
                $staff->zipcode = $request->input('zipcode');
                // $staff->department_id = $request->input('department_id');
                $staff->user_id = $request->input('profile_id');
            } else {
            }
            //$users->password =Hash::make($request['upassword']);
            $staff->save();
            $users->save();
            return response()->json([
                'success' => 'staff updated successfully'
            ]);
        }
    }
    public function updatepasword(Request $request, $id)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'current' => [
                'required', function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        return $fail(__('The current password is incorrect'));
                    }
                },
                'min:8',
                'max:30'
            ],
            'up_password' => 'required|min:8',
            'up_confirm_password' => 'required|min:8|same:up_password',
            // 'up_position' => 'required|max:255',
            // 'up_department_id' => 'required|max:255',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {

            $users = User::find($id);
            if (!empty($request->up_password)) {
                $users->password = Hash::make($request['up_password']);
            } else {
            }
            $users->save();
            // $staff->save();
            return response()->json([
                'success' => 'account updated successfully'
            ]);
        }
    }
    public function changeProfilePic(Request $request)
    {
        $path = 'user/';
        $file = $request->file('processor-profile_pic');
        $new_name = 'UIMG_' . date('Ymd') . uniqid() . '.jpg';

        $upload = $file->move(public_path($path), $new_name);

        if (!$upload) {
            return response()->json(['status' => 0, 'msg' => 'something went wrong, upload new picture failed']);
        } else {
            $oldPicture = User::find(Auth::user()->id)->getAttributes()['picture'];
            if ($oldPicture != '') {
                if (\file_exists(public_path($path . $oldPicture))) {
                    \unlink(public_path($path . $oldPicture));
                }
            }

            $update = User::find(Auth::user()->id)->update(['picture' => $new_name]);
            if (!$upload) {
                return response()->json(['status' => 0, 'msg' => 'something went wrong,updating picture in db failed']);
            } else {
                return response()->json(['status' => 1, 'msg' => 'your profile picture has been updated succesfully']);
            }
        }
    }
    public function purchase_request()
    {
        $id = Auth::user()->id;
        $user = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->select(
                'staff.id',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'staff.sex',
                'staff.barangay',
                'staff.municipality',
                'staff.city',
                'staff.province',
                'staff.zipcode',
                'users.position',
                'staff.contact_no',
                'departments.Dept_name',
                'departments.id',
                'buildings.Building_name',
                'buildings.id'
            )
            ->where('staff.id', '=', $id)
            ->first();
        $userr = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                'departments.Dept_name',
                'departments.id',
            )
            ->where('staff.id', '=', $id)
            ->first();
        // $rand = rand(10, 10000);
        // $generatePR = 'PR-' . date("Y-md") . '-' . $rand;
        $latestPR = PurchaseRequest::orderBy('created_at', 'DESC')->first();
        if ($latestPR == null) {
            $generatePR = 'PR-' . date("Y-md") . '-' . str_pad(0 + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $generatePR = 'PR-' . date("Y-md") . '-' . str_pad($latestPR->id + 1, 4, "0", STR_PAD_LEFT);
        }
        $purchaserequest = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->whereNotIn('purchase_request_histories.action', ['Request Denied', 'Approved', 'Request on Hold'])
            ->where('user_id', '=', $id)->get();
        $deny_pr = DeniedRequest::join('purchase_requests', 'denied_requests.pr_no', '=', 'purchase_requests.pr_no')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            ->where('purchase_requests.user_id', '=', $id)->get();
        $hold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.id')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Request on Hold')
            ->where('staff.id', '=', $id)
            // ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get();
        $approved_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Approved')
            ->where('user_id', '=', $id)->get();
        $items = Item::select('items.item_desc')->get()->toArray();
        // dd($deny_pr);
        return view('manage_purchase_request.purchase_request', compact('approved_pr', 'items', 'hold', 'deny_pr', 'generatePR', 'user', 'userr', 'purchaserequest'));
    }
    public function addrequisition(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'type_of_req' => 'required|max:255',
            'purpose' => 'required|max:255',
            // 'beggining' => 'required|max:255',
            // 'ending' => 'required|max:255',
            'quantity' => 'required|max:255',
            'unit' => 'required|max:255',
            'item_desc' => 'required|max:255',
            'pr_no' => 'unique:purchase_requests'

        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $name = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $approver = User::select('email')->where('position', '=', 'ASSD Manager')->get()->first();
            // dd($approver->email);
            $item = $request->input('item_desc');
            $distinct_item = Item::select('id')
                ->whereIn('item_desc', $item)
                ->get()->toArray();
            // for ($i = 0; $i < count($item); $i++) {
            //     $encode1 = $item;
            //     $encode2 = $distinct_item;
            // }
            // $array = array_values(array_diff_key($encode2, $encode1));
            // dd($distinct_item);
            for ($i = 0; $i < count($item); $i++) {
                $data = [
                    'item_desc' => ucwords($item[$i]),
                    'created_at' => now()
                ];
                // dd($data);
                DB::table('items')->upsert($data, 'item_desc');
            }

            $data = [
                'subject' => 'New Purchase Request',
                'body' => $request->input('pr_no'),
                'from' => $email,
                'name' => $name
            ];
            $pr_no = $request->input('pr_no');
            // $purpose = $request->input('purpose');
            $beggining = $request->input('beggining');
            $ending = $request->input('ending');
            $quantity = $request->input('quantity');
            $unit = $request->input('unit');
            $item_desc = $request->input('item_desc');
            for ($i = 0; $i < count($item_desc); $i++) {
                $datasave = [
                    'pr_no' => $pr_no,
                    'item_desc' => $item_desc[$i],
                    'beggining' => $beggining[$i],
                    'ending' => $ending[$i],
                    'quantity' => $quantity[$i],
                    'unit' => $unit[$i],
                ];
                // $pr_item-> save($datasave);
                DB::table('purchase_request_items')->insert($datasave);
            }
            $requisition = new PurchaseRequest;
            $requisition->pr_no = $request->input('pr_no');
            $requisition->type = $request->input('type_of_req');
            $requisition->purpose = $request->input('purpose');
            $requisition->remarks = 'pending';
            $requisition->department_id = $request->input('department');
            $requisition->user_id = Auth::user()->id;
            $prh = new PurchaseRequestHistory;
            $prh->pr_no = $request->input('pr_no');
            $prh->action = 'New Purchase Request';
            $canvass = new Canvass;
            $canvass->pr_no = $request->input('pr_no');
            Mail::to($approver->email)->send(new GmailNotification($data));
            $requisition->save();
            $prh->save();
            $canvass->save();

            return response()->json([
                'success' => 'Requisition added successfully'
            ]);
        }
    }
    public function view_purchase_request($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($output);

        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_purchase_requesttt', ['user_email' => $user_email, 'output' => $output, 'outputs' => $outputs]);
    }
    public function pr_for_canvass()
    {
        $pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'For Canvassing')->get();
        $verifying_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verifying')
            ->get();
        return view('manage_purchase_request.pr_for_canvass', compact('pr', 'verifying_pr'));
    }
    public function view_pr_for_canvass($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('users', 'purchase_requests.user_id', '=', 'users.id')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();

        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $canvass_pr = SupplierItem::join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->select(
                'purchase_request_items.pr_no',
                'supplier_items.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->groupBy(
                'purchase_request_items.pr_no',
                'supplier_items.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name'
            )
            ->get()
            ->sortBy('item_desc')
            ->toArray();
        $canvass = [];
        foreach ($canvass_pr as $dataaa) {
            $canvass[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $canvass_per_supp = SupplierItem::join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->select(
                'purchase_request_items.pr_no',
                'supplier_items.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->groupBy(
                'purchase_request_items.pr_no',
                'supplier_items.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name'
            )
            ->get()
            ->sortBy('item_desc')
            ->toArray();
        $Collection = collect($canvass_per_supp);
        $groupCollection = $Collection->groupBy('item_desc')->values();
        $gg = collect($groupCollection)->all();

        $check_item = SupplierItem::join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->select('purchase_request_items.item_desc')
            ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->groupby('purchase_request_items.item_desc')
            ->get()
            ->toArray();
        $item = PurchaseRequestItem::select('item_desc')
            ->whereIn('item_desc', $check_item)
            ->where('pr_no', '=', $pr_no)
            ->groupby('item_desc')
            ->get()
            ->toArray();
        $nn = PurchaseRequestItem::select('purchase_request_items.item_desc')
            ->whereNotIn('purchase_request_items.item_desc', $check_item)
            ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('purchase_request_items.chk_item', '!=', null)
            ->get()
            ->toArray();
        // dd($check_item);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_for_canvass', ['item' => $item, 'nn' => $nn, 'pr_infos' => $pr_infos, 'gg' => $gg, 'user_email' => $user_email, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvass' => $canvass]);
    }
    public function sendcanvass(Request $request, $pr_no)
    {
        $email = Auth::user()->email;
        $role = User::join('staff', 'users.id', '=', 'staff.user_id')
            ->select('staff.fname', 'staff.lname')
            ->where('users.id', '=', Auth::user()->id)
            ->get()->first();
        $sendto = User::where('position', '=', 'ASSD Manager')
            ->get()->first();
        $reqemail = $request->input('email');
        $data = [
            'subject' => 'PR has been Canvassed',
            'body' => $pr_no,
            'from' => $email,
            'name' => $role->fname . ' ' . $role->lname,
        ];
        $canvass_pr = SupplierItem::join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->select(
                'items.item_desc',
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->orderby('items.item_desc')
            ->get()
            // ->unique()
            ->toArray();

        $Collection = collect($canvass_pr);
        $groupCollection = $Collection->unique()->values();
        $cpr = collect($groupCollection)->all();

        $si = Item::join('supplier_items', 'items.id', '=', 'supplier_items.item_id')
            ->select('items.item_desc')
            ->whereIn('supplier_items.id', $request->input('checkbox_canvass'))
            ->orderby('items.item_desc')
            ->get()
            ->toArray();
        $Collection = collect($si);
        $groupCollection = $Collection->unique()->values();
        $cccc = collect($groupCollection)->all();

        if (json_encode($cpr) != json_encode($cccc)) {
            session()->flash('error', 'incompelte canvass.');
            return response()->json([
                'status' => 'error',
                'message' => 'incompelte canvass.'
            ], 400);
        }
        // dd($request->all());
        $canvass_no = Canvass::where('pr_no', '=', $pr_no)->value('canvass_no');
        $cc = $request->input('checkbox_canvass');
        $ci = $request->input('canvass_item');
        $up = $request->input('update_price');
        // dd($cc);
        for ($i = 0; $i < count($cc); $i++) {
            $canvass = new CanvassItem();
            $canvass->canvass_no = $canvass_no;
            $canvass->supplier_items_id = $cc[$i];
            // $canvass->item_desc = $ci[$i];
            $canvass->selected = null;
            $canvass->save();
        }
        for ($i = 0; $i < count($cc); $i++) {
            $datasave = [
                'offered_price' => $up[$i]
            ];
            // dd($datasave);
            DB::table('supplier_items')->where('id', $cc[$i])->update($datasave);
        }
        $prh = PurchaseRequestHistory::where('pr_no', '=', $pr_no)->first();
        $prh->action = 'Verifying';
        $prh->update();
        Mail::to($sendto->email)->send(new GmailNotification($data));
        Mail::to($reqemail)->send(new Requestor($data));
        return response()->json([
            'success' => 'Requisition added successfully'
        ]);
    }
    public function view_canvassed($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $canvass_info =  SupplierItem::join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'purchase_request_items.pr_no',
                'supplier_items.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)->get()
            ->unique()
            ->toArray();
        $canvass_output = [];
        foreach ($canvass_info as $dataa) {
            $canvass_output[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'canvass_items.id',
                'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get()->toArray();
        $outputss = [];
        foreach ($pr_infoss as $dataaa) {
            $outputss[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($outputss);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.update_pr_for_canvass', ['user_email' => $user_email, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvass_output' => $canvass_output, 'outputss' => $outputss]);
    }
    public function update_canvassed(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'supplier_items_id' => 'unique:canvass_items|in:1'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {

            $canvass_no = Canvass::where('pr_no', '=', $pr_no)->value('canvass_no');
            $update_canvass = $request->input('update_canvass');
            // dd($update_canvass);
            for ($i = 0; $i < count($update_canvass); $i++) {
                $update_canvass_item = new CanvassItem();
                $update_canvass_item->canvass_no = $canvass_no;
                $update_canvass_item->supplier_items_id = $update_canvass[$i];
                $update_canvass_item->selected = null;
                $update_canvass_item->save();
            }

            return response()->json([
                'success' => 'added successfully',
            ]);
        }
    }
    public function delete_canvassed_item($id)
    {
        $dci = CanvassItem::find($id);
        $dci->delete();
        return response()->json([
            'success' => 'account deleted successfully'
        ]);
    }
    public function delete_canvass_item($id)
    {
        $dci = CanvassItem::find($id);
        $dci->delete();
        return response()->json([
            'success' => 'account deleted successfully'
        ]);
    }
    public function approved_pr()
    {
        $po = PurchaseOrder::select('*')->where('purchase_orders.status', '=', "Need to Prepare")->get();
        $approved_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Approved')
            ->get();
        $pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'New Purchase Request')
            ->get();
        $canvass_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'For Canvassing')->get();
        $verifying_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verifying')
            ->get();
        $verify_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verifying')
            ->get();
        $verified_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verified')
            ->get();
        $checkfund_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Verified')->get();
        $checked_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Checked')
            ->get();
        $approval_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Checked')
            ->get();
        $approved_prr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Approved')
            ->get();

        // dd($approved_prr);
        return view('manage_purchase_request.approved_pr', compact('approved_prr', 'approval_pr', 'checked_pr', 'checkfund_pr', 'approved_pr', 'po', 'pr', 'canvass_pr', 'verifying_pr', 'verify_pr', 'verified_pr'));
    }
    public function view_approved_pr(Request $request, $pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        // $rand = rand(10, 10000);
        // $generatePO = 'PO-' . date("Y-md") . '-' . $rand;
        $latestPO = PurchaseOrder::orderBy('created_at', 'DESC')->first();
        if ($latestPO == null) {
            $generatePO = 'PO-' . date("Y-md") . '-' . str_pad(0 + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $generatePO = 'PO-' . date("Y-md") . '-' . str_pad($latestPO->id + 1, 4, "0", STR_PAD_LEFT);
        }
        // dd($generatePO);
        $po_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select('*')->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $po = [];
        foreach ($po_info as $dataaa) {
            $po[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckFund', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $isapproved2 = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved2', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapproved2By = [];
        foreach ($isapproved2 as $dataa) {
            $isapproved2By[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($isapproved2By);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_approved_pr', ['pr_infos' => $pr_infos, 'user_email' => $user_email, 'isapproved2By' => $isapproved2By, 'isapprovedBy' => $isapprovedBy, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy, 'po' => $po, 'generatePO' => $generatePO, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function save_po(Request $request, $pr_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            // dd($pr_no);
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            // $sendto = User::where('position', '=', 'Finance Head')
            //     ->get()->first();
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            $po_no = $request->name;
            $data = [
                'subject' => 'Generated Purchase Order',
                'body' => $pr_no,
                'po' => $po_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $po = new PurchaseOrder;
            $po->po_no = $request->name;
            $po->pr_no = $pr_no;
            $po->status = 'Need to Prepare';
            $po->createdDate = now();
            $po->save();
            $pr = PurchaseRequest::where('pr_no', '=', $pr_no)->first();
            $pr->w_PO = 1;
            $pr->update();
            // Mail::to($sendto->email)->send(new SelectSupplier($data));
            Mail::to($reqemail->email)->send(new GeneratedPO($data));

            $sendto = User::where('position', '=', 'Finance Head')
                ->get()->first();
            $pr_no = $request->input('pr_no');
            $data = [
                'subject' => 'Purchase Order has been Prepared',
                'body' => $pr_no,
                'po' => $po_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            // $id = $request->input('id');
            $id = Auth::user()->id;
            $preparedate = now();
            DB::table('purchase_orders')->where('po_no', $po_no)->update(['preparedBy' => $id, 'preparedDate' => $preparedate, 'status' => "Prepared"], true);
            Mail::to($sendto->email)->send(new GeneratedPO($data));
            Mail::to($reqemail->email)->send(new Requestor_PO($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function po_form(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'purchase_requests.user_id',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                'purchase_orders.paymentTerm',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
                'suppliers.business_add',
                'suppliers.email',
                'suppliers.contact_no',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        // dd($po_output);
        $pb = Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.description', '=', 'Processor')->get()->toArray();
        return view('manage_purchase_order.prepare_po', ['pb' => $pb, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'po_output' => $po_output]);
    }
    public function prepare_po(Request $request, $po_no)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Finance Head')
                ->get()->first();
            $pr_no = $request->input('pr_no');
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            $po_no = $request->input('po_no');
            $data = [
                'subject' => 'Purchase Order has been Prepared',
                'body' => $pr_no,
                'po' => $po_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $id = $request->input('id');
            $preparedate = now();
            DB::table('purchase_orders')->where('po_no', $po_no)->update(['preparedBy' => $id, 'preparedDate' => $preparedate, 'status' => "Prepared"], true);
            Mail::to($sendto->email)->send(new GeneratedPO($data));
            Mail::to($reqemail->email)->send(new Requestor_PO($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function approved_po()
    {
        $purchase_order = PurchaseOrder::select('*')->get();
        $po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Prepared')
            ->get();
        $verified_po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Verified')
            ->get();
        $approving = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approving')
            ->get();
        $approved_po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approved')
            ->get();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $poforapproval = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Verified')
            ->get()
            ->count();
        // dd($purchase_order);
        return view('manage_purchase_order.prepared_po', compact('poforapproval', 'purchase_order', 'po', 'verified_po', 'user', 'approved_po', 'approving'));
    }
    public function view_prepared_po(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'purchase_requests.user_id',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                'purchase_orders.paymentTerm',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'supplier_items.supplier_id',
                'suppliers.business_name',
                'suppliers.business_add',
                'suppliers.email',
                'suppliers.contact_no',
                'suppliers.email',
                'suppliers.contact_no',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $Collection = collect($pr_infoss);
        $groupCollection = $Collection->groupBy('business_name')->values();
        $gg = collect($groupCollection)->all();
        // dd($gg);
        return view('manage_purchase_order.view_prepared_po', ['gg' => $gg, 'user' => $user, 'output' => $output, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'po_output' => $po_output]);
    }
    public function view_verified_po(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                'purchase_orders.paymentTerm',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'purchase_request_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()->toArray();

        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        // dd($user);
        return view('manage_purchase_order.view_verified_po', ['user' => $user, 'output' => $output, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'po_output' => $po_output]);
    }
    public function view_approved_po(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $approve = PurchaseOrder::join('staff', 'purchase_orders.approvedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approvedBy = [];
        foreach ($approve as $dataa) {
            $approvedBy[] = $dataa;
        }
        $approve2 = PurchaseOrder::join('staff', 'purchase_orders.approved2By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved2By = [];
        foreach ($approve2 as $dataa) {
            $approved2By[] = $dataa;
        }
        $approve3 = PurchaseOrder::join('staff', 'purchase_orders.approved3By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved3By = [];
        foreach ($approve3 as $dataa) {
            $approved3By[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            // ->join('orders', 'supplier_items.supplier_id', '=', 'orders.supplier_id')
            ->select(
                // '*',
                'purchase_requests.user_id',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                // 'orders.payment_term',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                'canvass_items.supplier_items_id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.supplier_id',
                'supplier_items.offered_price',
                'suppliers.business_name',
                'suppliers.business_add',
                'suppliers.email',
                'suppliers.contact_no',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $pb = Staff::join('users', 'staff.user_id', '=', 'users.id')
            ->join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->where('roles.description', '=', 'Processor')->get()->toArray();
        // dd($pb);
        $Collection = collect($pr_infoss);
        $groupCollection = $Collection->groupBy('business_name')->values();
        $gg = collect($groupCollection)->all();
        return view('manage_purchase_order.view_verified_po', ['gg' => $gg, 'pb' => $pb, 'user' => $user, 'output' => $output, 'approved3By' => $approved3By, 'approved2By' => $approved2By, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'approvedBy' => $approvedBy, 'po_output' => $po_output]);
    }
    public function view_new_pr($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select(
                '*',
                'purchase_request_items.id'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $check_item = Item::select('item_desc', 'id')
            ->groupBy('item_desc', 'id')
            ->get();
        $item_outputs = [];
        foreach ($check_item as $dataa) {
            $item_outputs[] = $dataa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        // dd($output);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_new_purchase_request', ['user' => $user, 'output' => $output, 'outputs' => $outputs, 'item_outputs' => $item_outputs]);
    }
    public function view_pr_for_verification($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $canvass_info = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'canvass_items.id',
                // 'items.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->sortBy('items.item_desc')->toArray();
        $canvass_output = [];
        foreach ($canvass_info as $dataa) {
            $canvass_output[] = $dataa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }

        // dd($isverifyBy);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_for_verification', ['user' => $user, 'isverifyBy' => $isverifyBy, 'output' => $output, 'outputs' => $outputs, 'canvass_output' => $canvass_output]);
    }
    public function update_pr_for_verification($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $canvass_info = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'canvass_items.id',
                'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $canvass_output = [];
        foreach ($canvass_info as $dataa) {
            $canvass_output[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                'canvass_items.id',
                'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            ->get()->toArray();
        $outputss = [];
        foreach ($pr_infoss as $dataaa) {
            $outputss[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        // dd($isverifyBy);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.update_pr_for_verification', ['user' => $user, 'isverifyBy' => $isverifyBy, 'output' => $output, 'outputs' => $outputs, 'canvass_output' => $canvass_output, 'outputss' => $outputss]);
    }
    public function view_pr_check_fund($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }

        // dd($canvassed_item);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_check_fund', ['isverifyBy' => $isverifyBy, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function view_pr_checked_fund($pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckfund', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        // dd($ischeckfundBy);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_checked_fund', ['ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function view_pr_for_approval(Request $request, $pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckfund', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        // dd($canvassed_item);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.view_pr_for_approval', ['isapprovedBy' => $isapprovedBy, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy, 'user' => $user, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function view_approved_prr(Request $request, $pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $pr_infoss = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->orderBy('canvass_items.id','desc')
            ->get()
            ->unique()
            ->toArray();

        $canvassed_item = [];
        foreach ($pr_infoss as $dataaa) {
            $canvassed_item[] = $dataaa;
        }
        // $rand = rand(10, 10000);
        // $generatePO = 'PO-' . date("Y-md") . '-' . $rand;
        $latestPO = PurchaseOrder::orderBy('created_at', 'DESC')->first();
        if ($latestPO == null) {
            $generatePO = 'PO-' . date("Y-md") . '-' . str_pad(0 + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $generatePO = 'PO-' . date("Y-md") . '-' . str_pad($latestPO->id + 1, 4, "0", STR_PAD_LEFT);
        }
        // dd($generatePO);
        $po_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select('*')->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $po = [];
        foreach ($po_info as $dataaa) {
            $po[] = $dataaa;
        }
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckFund', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $isapproved2 = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved2', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapproved2By = [];
        foreach ($isapproved2 as $dataa) {
            $isapproved2By[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($isapproved2By);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_order.view_approved_pr', ['pr_infos' => $pr_infos, 'user_email' => $user_email, 'isapproved2By' => $isapproved2By, 'isapprovedBy' => $isapprovedBy, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy, 'po' => $po, 'generatePO' => $generatePO, 'output' => $output, 'outputs' => $outputs, 'canvassed_item' => $canvassed_item]);
    }
    public function pr_to_po()
    {
        $po = PurchaseOrder::select('*')->where('purchase_orders.status', '=', "Need to Prepare")->get();
        $approved_pr = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Approved')
            ->where('purchase_requests.w_PO', '=', null)
            ->get();
        return view('manage_purchase_order.pr_to_po', compact('approved_pr', 'po'));
    }
    public function view_track_pr($pr_no)
    {
        $ischeckitem = PurchaseRequestItem::join('items', 'purchase_request_items.item_id', '=', 'items.id')
            ->select(
                'purchase_request_items.item_id',
                'purchase_request_items.updated_at'
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckitemBy = [];
        foreach ($ischeckitem as $dataa) {
            $ischeckitemBy[] = $dataa;
        }
        $isverify = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isverifyBy = [];
        foreach ($isverify as $dataa) {
            $isverifyBy[] = $dataa;
        }
        $ischeckfund = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isCheckFund', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $ischeckfundBy = [];
        foreach ($ischeckfund as $dataa) {
            $ischeckfundBy[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $isapproved2 = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved2', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapproved2By = [];
        foreach ($isapproved2 as $dataa) {
            $isapproved2By[] = $dataa;
        }
        $canvass = PurchaseRequest::join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $canvassed = [];
        foreach ($canvass as $dataa) {
            $canvassed[] = $dataa;
        }
        $isapproved = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isApproved', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $isapprovedBy = [];
        foreach ($isapproved as $dataa) {
            $isapprovedBy[] = $dataa;
        }
        $pr = PurchaseRequest::join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                '*',
                'purchase_requests.created_at'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();

        $id = Auth::user()->id;
        $user = PurchaseRequest::join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get()
            ->toArray();
        $processor = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('roles.name', '=', 'Processor')
            ->get()->toArray();
        $reqdenied = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->select(
                'purchase_request_histories.action',
                'purchase_requests.updated_at',
                'purchase_request_histories.isVerified',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            // ->where('purchase_request_histories.isVerified','=',$id)
            ->get()->toArray();
        $vv = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')

            ->select(
                'purchase_request_histories.isVerified',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            // ->where('purchase_request_histories.isVerified','=',$id)
            ->get()->toArray();
        $deniedby = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isVerified', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'purchase_request_histories.updated_at',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('purchase_request_histories.action', '=', 'Request Denied')
            ->where('purchase_request_histories.isVerified', '=', $vv)
            ->get()->toArray();
        $checkitemby = PurchaseRequestItem::join('items', 'purchase_request_items.item_id', '=', 'items.id')
            ->join('staff', 'purchase_request_items.checkitemby', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
            )
            ->where('purchase_request_items.pr_no', '=', $pr_no)->get()->toArray();
        $hold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_request_histories.action', '=', 'Request on Hold')
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get();
        $ishold = PurchaseRequest::join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->join('staff', 'purchase_request_histories.isHold', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_app = PurchaseRequest::select('pr_no')->where('user_id', '=', Auth::user()->id)->get()->toArray();
        $prp = PurchaseRequest::select(
            '*',
            'purchase_requests.created_at'
        )
            ->whereIn('pr_no', $pr_app)
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($prp);
        // return view('manage_purchase_request.view_purchase_request',['output'=>$output]);
        return view('manage_purchase_request.track_pr', ['prp' => $prp, 'pr_app' => $pr_app, 'ishold' => $ishold, 'hold' => $hold, 'checkitemby' => $checkitemby, 'deniedby' => $deniedby, 'reqdenied' => $reqdenied, 'ischeckitemBy' => $ischeckitemBy, 'isapprovedBy' => $isapprovedBy, 'processor' => $processor, 'user' => $user, 'canvassed' => $canvassed, 'pr' => $pr, 'isapproved2By' => $isapproved2By, 'isapprovedBy' => $isapprovedBy, 'ischeckfundBy' => $ischeckfundBy, 'isverifyBy' => $isverifyBy]);
    }
    public function purchase_order()
    {
        $id = Auth::user()->id;
        $purchaseorder = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select('*')
            ->where('user_id', '=', $id)->get();
        // dd($purchaseorder);
        return view('manage_purchase_order.purchase_order', compact('purchaseorder'));
    }
    public function view_track_po($pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $po = [];
        foreach ($pr_info as $data) {
            $po[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select('*')
            ->where('purchase_orders.pr_no', '=', $pr_no)
            ->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $approve = PurchaseOrder::join('staff', 'purchase_orders.approvedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approvedBy = [];
        foreach ($approve as $dataa) {
            $approvedBy[] = $dataa;
        }
        $approve2 = PurchaseOrder::join('staff', 'purchase_orders.approved2By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved2By = [];
        foreach ($approve2 as $dataa) {
            $approved2By[] = $dataa;
        }
        $approve3 = PurchaseOrder::join('staff', 'purchase_orders.approved3By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved3By = [];
        foreach ($approve3 as $dataa) {
            $approved3By[] = $dataa;
        }
        $orderdate = PurchaseOrder::where('purchase_orders.pr_no', '=', $pr_no)
            ->where('purchase_orders.orderDate', '!=', null)->get()->toArray();
        $orderDate = [];
        foreach ($orderdate as $dataa) {
            $orderDate[] = $dataa;
        }

        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $po_pro = PurchaseRequest::join('purchase_orders', 'purchase_requests.pr_no', '=', 'purchase_orders.pr_no')
            ->select('purchase_orders.pr_no')->where('purchase_requests.user_id', '=', Auth::user()->id)->get()->toArray();
        $pop = PurchaseRequest::join('purchase_orders', 'purchase_requests.pr_no', '=', 'purchase_orders.pr_no')
            ->select(
                '*',
                'purchase_requests.created_at'
            )
            ->whereIn('purchase_orders.pr_no', $po_pro)
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($pop);
        return view('manage_purchase_order.track_po', ['orderDate' => $orderDate, 'pop' => $pop, 'user' => $user, 'po' => $po, 'approved3By' => $approved3By, 'approved2By' => $approved2By, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'approvedBy' => $approvedBy]);
    }
    public function order_po()
    {
        $approved_po = PurchaseOrder::select('*')
            ->where('purchase_orders.status', '=', 'Approved')
            ->get();
        $ordered_po = PurchaseOrder::join('orders', 'purchase_orders.po_no', '=', 'orders.po_no')
            ->select('*')
            ->whereIn('orders.stat', ['Ordered', 'Partially Delivered'])
            ->get();

        // dd($ordered_po);
        return view('manage_order.order_po', compact('approved_po', 'ordered_po'));
    }
    public function view_po_to_order(Request $request, $pr_no)
    {
        $pr_info = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            // ->join('staff','purchase_orders.preparedBy','=','staff.user_id')
            ->select(
                '*',
                // 'purchase_orders.po_no',
                // 'purchase_orders.pr_no',
                // 'purchase_orders.createdDate',

            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $approve = PurchaseOrder::join('staff', 'purchase_orders.approvedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approvedBy = [];
        foreach ($approve as $dataa) {
            $approvedBy[] = $dataa;
        }
        $approve2 = PurchaseOrder::join('staff', 'purchase_orders.approved2By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved2By = [];
        foreach ($approve2 as $dataa) {
            $approved2By[] = $dataa;
        }
        $approve3 = PurchaseOrder::join('staff', 'purchase_orders.approved3By', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approved3By = [];
        foreach ($approve3 as $dataa) {
            $approved3By[] = $dataa;
        }
        $pr_infoss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                'purchase_orders.paymentTerm',
                'purchase_requests.purpose',
                'purchase_requests.user_id',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
                'suppliers.business_add',
                // 'staff.fname',
                // 'staff.mname',
                // 'staff.lname',
                // 'users.position',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            ->get()
            ->unique()
            ->toArray();
        $po_output = [];
        foreach ($pr_infoss as $dataaa) {
            $po_output[] = $dataaa;
        }
        $pr_per_sup = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->join('orders', 'supplier_items.supplier_id', '=', 'orders.supplier_id')
            ->select(
                'suppliers.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'supplier_items.supplier_id',
                'suppliers.business_name',
                'suppliers.business_add',
                'orders.payment_term',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->where('orders.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            ->groupBy(
                'suppliers.id',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'supplier_items.supplier_id',
                'suppliers.business_name',
                'orders.payment_term',
                'suppliers.business_add',
            )
            ->get()
            ->toArray();
        $Collection = collect($pr_per_sup);
        $groupCollection = $Collection->groupBy('business_name')->values();
        $gg = collect($groupCollection)->all();
        // $gg->toArray();
        // dd($gg);

        $pr_infosss = PurchaseOrder::join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('canvasses', 'purchase_requests.pr_no', '=', 'canvasses.pr_no')
            ->join('canvass_items', 'canvasses.canvass_no', '=', 'canvass_items.canvass_no')
            ->join('supplier_items', 'canvass_items.supplier_items_id', '=', 'supplier_items.id')
            ->join('purchase_request_items', 'supplier_items.item_id', '=', 'purchase_request_items.item_id')
            ->join('items', 'supplier_items.item_id', '=', 'items.id')
            ->join('suppliers', 'supplier_items.supplier_id', '=', 'suppliers.id')
            ->select(
                // '*',
                // 'purchase_orders.po_no',
                'purchase_orders.pr_no',
                'purchase_orders.status',
                'purchase_orders.createdDate',
                // 'purchase_orders.paymentTerm',
                'purchase_requests.purpose',
                'purchase_orders.preparedBy',
                'purchase_orders.verifiedBy',
                'purchase_orders.approvedBy',
                'canvass_items.id',
                // 'canvass_items.canvass_no',
                'items.item_desc',
                'supplier_items.brand',
                'supplier_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'canvass_items.quantity',
                'supplier_items.offered_price',
                'suppliers.business_name',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            // ->where('purchase_request_items.pr_no', '=', $pr_no)
            ->where('canvass_items.selected', '=', '1')
            // ->where($where)
            // ->groupBy(DB::raw('suppliers.business_name'))
            ->get()
            ->unique()
            ->toArray();

        $po_per_supp = [];
        foreach ($pr_infosss as $dataaa) {
            $po_per_supp[] = $dataaa;
        }
        // dd($po_per_supp);

        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $exten = Order::select('id')->where('pr_no', $pr_no)->get()->toArray();

        return view('manage_order.view_po_to_order', ['exten' => $exten, 'gg' => $gg, 'user' => $user, 'output' => $output, 'approved3By' => $approved3By, 'approved2By' => $approved2By, 'preparedBy' => $preparedBy, 'verifiedBy' => $verifiedBy, 'approvedBy' => $approvedBy, 'po_output' => $po_output]);
    }
    public function send_order(Request $request, $po_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            // $sendto = User::where('position', '=', 'Procurement Officer')
            //     ->get()->first();
            $pr_no = $request->input('pr_no');
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            $po_no = $request->input('po_no');

            $data = [
                'subject' => 'Purchase Order has been Ordered - ',
                'body' => $pr_no,
                'po' => $po_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $a = $request->input('ordered_po');
            $b = $request->input('supplier_id');

            $c = $request->input('sid');
            $d = $request->input('item_desc');
            $e = $request->input('brand');
            $f = $request->input('unit');
            $g = $request->input('quantity');
            $h = $request->input('price');

            for ($i = 0; $i < count($a); $i++) {
                $order_data = [
                    'order_no' => $a[$i],
                    'stat' => 'Ordered',
                ];
                $date = now();
                DB::table('purchase_orders')->where('po_no', $po_no)->update(['orderDate' => $date, 'status' => 'Ordered'], true);
                DB::table('orders')->where('pr_no', $pr_no)->where('supplier_id', $b[$i])->update($order_data, 'supplier_id');
            }
            for ($i = 0; $i < count($c); $i++) {
                $order_items = [
                    'item_desc' => $d[$i],
                    'brand' => $e[$i],
                    'unit' => $f[$i],
                    'quantity' => $g[$i],
                    'price' => $h[$i],
                    'supplier_id' => $c[$i],
                    'pr_no' => $pr_no,

                ];
                DB::table('order_items')->insert($order_items);
            }
            Mail::to($reqemail->email)->send(new Requestor_PO($data));
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function view_ordered_po(Request $request, $pr_no, $order_no, $sid)
    {
        $order_info = Order::join('order_items', 'orders.pr_no', '=', 'order_items.pr_no')
            ->join('suppliers', 'order_items.supplier_id', '=', 'suppliers.id')
            ->join('purchase_orders', 'orders.pr_no', '=', 'purchase_orders.pr_no')
            // ->join('deliveries', 'purchase_orders.po_no', '=', 'deliveries.po_no')
            ->join('order_item_quantities', 'order_items.id', '=', 'order_item_quantities.item_id')
            ->select(
                'order_items.id',
                'orders.order_no',
                'order_items.item_desc',
                'order_items.brand',
                'order_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'order_items.quantity',
                'order_items.price',
                'order_items.supplier_id',
                // 'order_item_quantities.delivered',
                'suppliers.business_name',
                'orders.payment_term',
                'orders.pr_no',
                'purchase_orders.orderDate',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
                DB::raw('sum(order_item_quantities.delivered)as delivered'),
                // 'order_items.item_quantity',
                // 'order_items.id as di',
            )
            ->where('orders.pr_no', '=', $pr_no)
            ->where('orders.order_no', '=', $order_no)
            ->where('order_items.supplier_id', '=', $sid)
            ->groupby(
                'order_items.id',
                'orders.order_no',
                'order_items.item_desc',
                'order_items.brand',
                'order_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'order_items.quantity',
                'order_items.price',
                'order_items.supplier_id',
                // 'order_item_quantities.delivered',
                'suppliers.business_name',
                'orders.payment_term',
                'orders.pr_no',
                'purchase_orders.orderDate',
                'purchase_orders.po_no',
                'purchase_orders.pr_no',
            )
            ->get()
            // ->unique()
            ->toArray();
        // dd($order_info);
        if (empty($order_info)) {
            $order_info = Order::join('order_items', 'orders.pr_no', '=', 'order_items.pr_no')
                ->join('suppliers', 'order_items.supplier_id', '=', 'suppliers.id')
                ->join('purchase_orders', 'orders.pr_no', '=', 'purchase_orders.pr_no')
                ->select(
                    '*'
                )
                ->where('orders.pr_no', '=', $pr_no)
                ->where('orders.order_no', '=', $order_no)
                ->where('order_items.supplier_id', '=', $sid)

                ->get()
                ->unique()
                ->toArray();
            $order_infoo = Order::join('order_items', 'orders.pr_no', '=', 'order_items.pr_no')
                ->join('suppliers', 'order_items.supplier_id', '=', 'suppliers.id')
                ->join('purchase_orders', 'orders.pr_no', '=', 'purchase_orders.pr_no')
                // ->join('deliveries', 'purchase_orders.po_no', '=', 'deliveries.po_no')
                // ->join('order_item_quantities', 'order_items.id', '=', 'order_item_quantities.item_id')
                ->select(
                    'order_items.id',
                    'orders.order_no',
                    'order_items.item_desc',
                    'order_items.brand',
                    'order_items.unit',
                    'suppliers.email',
                    'suppliers.contact_no',
                    'order_items.quantity',
                    'order_items.price',
                    'order_items.supplier_id',
                    'order_items.delivered',
                    'suppliers.business_name',
                    'orders.payment_term',
                    'orders.pr_no',
                    'purchase_orders.orderDate',
                    'purchase_orders.po_no',
                    // DB::raw('sum(order_item_quantities.delivered)as delivered'),
                    // 'order_items.item_quantity',
                    // 'order_items.id as di',
                )
                ->where('orders.pr_no', '=', $pr_no)
                ->where('orders.order_no', '=', $order_no)
                ->where('order_items.supplier_id', '=', $sid)
                ->groupby(
                    'order_items.id',
                    'orders.order_no',
                    'order_items.item_desc',
                    'order_items.brand',
                    'order_items.unit',
                    'suppliers.email',
                    'suppliers.contact_no',
                    'order_items.quantity',
                    'order_items.price',
                    'order_items.supplier_id',
                    'order_items.delivered',
                    'suppliers.business_name',
                    'orders.payment_term',
                    'orders.pr_no',
                    'purchase_orders.orderDate',
                    'purchase_orders.po_no',
                )
                ->get()
                // ->unique()
                ->toArray();
            $Collection = collect($order_infoo);
            $groupCollection = $Collection->groupBy('business_name')->values();
            $gg = collect($groupCollection)->all();
        } else {
            $Collection = collect($order_info);
            $groupCollection = $Collection->groupBy('business_name')->values();
            $gg = collect($groupCollection)->all();
        }
        // dd($order_info);
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $rand = rand(10, 10000);
        $generatedeliveryno = 'DLN' . date("Y-md") . $rand;
        $delivery = PurchaseOrder::join('deliveries', 'purchase_orders.po_no', '=', 'deliveries.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select(
                'deliveries.delivery_no'
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)
            ->get()->toArray();

        $deliver = [];
        foreach ($delivery as $dataaa) {
            $deliver[] = $dataaa;
        }
        // dd($order_no);
        $delivery_detailss = PurchaseOrder::join('delivery_items', 'purchase_orders.po_no', '=', 'delivery_items.po_no')
            ->join('suppliers', 'delivery_items.supplier_id', '=', 'suppliers.id')
            ->join('orders', 'purchase_orders.po_no', '=', 'orders.po_no')
            ->select(
                '*',
                'delivery_items.id',
                'delivery_items.created_at',
            )
            ->where('purchase_orders.pr_no', '=', $pr_no)
            ->where('orders.order_no', '=', $order_no)
            // ->where('delivery_items.delivery_no', '=', $dln)
            ->get()
            ->toArray();

        $Collection = collect($delivery_detailss);
        $groupCollection = $Collection->groupBy('delivery_no')->values();
        $dl = collect($groupCollection)->all();

        $dl_no = PurchaseOrder::join('delivery_items', 'purchase_orders.po_no', '=', 'delivery_items.po_no')
            ->join('suppliers', 'delivery_items.supplier_id', '=', 'suppliers.id')
            ->join('orders', 'purchase_orders.po_no', '=', 'orders.po_no')
            // ->join('order_item_quantities', 'orders.order_no', '=', 'order_item_quantities.order_no')
            ->select(
                '*',
                'delivery_items.delivery_no',
                'delivery_items.created_at',
                'orders.order_no',
            )
            ->where('purchase_orders.pr_no', '=', $pr_no)
            // ->where('delivery_items.delivery_no', '=', $dln)
            ->where('delivery_items.supplier_id', '=', $sid)
            ->where('orders.order_no', '=', $order_no)
            ->where('delivery_items.item_quantity', '!=', null)
            // ->where('order_item_quantities.delivered', '!=', null)
            ->orderBy('delivery_items.created_at', 'ASC')
            ->get()
            ->toArray();
        $Collection = collect($dl_no);
        $groupCollection = $Collection->groupBy('delivery_no')->values();
        $dl_noo = collect($groupCollection)->all();
        // dd($dl_no);
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $approve = PurchaseOrder::join('staff', 'purchase_orders.approvedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approvedBy = [];
        foreach ($approve as $dataa) {
            $approvedBy[] = $dataa;
        }
        $chk_dl = Order::join('order_items', 'orders.pr_no', '=', 'order_items.pr_no')
            ->join('suppliers', 'order_items.supplier_id', '=', 'suppliers.id')
            ->join('purchase_orders', 'orders.pr_no', '=', 'purchase_orders.pr_no')
            ->join('order_item_quantities', 'order_items.id', '=', 'order_item_quantities.item_id')
            ->select(
                DB::raw('sum(order_item_quantities.delivered)as delivered'),
                'order_items.item_desc'
            )
            ->where('orders.pr_no', '=', $pr_no)
            ->where('orders.order_no', '=', $order_no)
            ->where('order_items.supplier_id', '=', $sid)
            ->groupby(
                'order_items.item_desc',
                'order_items.id',
            )
            ->orderby('order_items.item_desc')
            ->get()
            ->toArray();

        $chk_dl2 = Order::join('order_items', 'orders.pr_no', '=', 'order_items.pr_no')
            ->join('suppliers', 'order_items.supplier_id', '=', 'suppliers.id')
            ->join('purchase_orders', 'orders.pr_no', '=', 'purchase_orders.pr_no')
            ->join('order_item_quantities', 'order_items.id', '=', 'order_item_quantities.item_id')
            ->select(
                'order_items.quantity as delivered',
                'order_items.item_desc',
            )
            ->where('orders.pr_no', '=', $pr_no)
            ->where('orders.order_no', '=', $order_no)
            ->where('order_items.supplier_id', '=', $sid)
            ->groupby(
                'order_items.quantity',
                'order_items.id',
                'order_items.item_desc'
            )
            ->orderby('order_items.item_desc')
            ->get()
            ->toArray();
        $latestFN = Forwarded::orderBy('id', 'DESC')->first();
        if ($latestFN == null) {
            $generateforwardno = 'FN' . date("Y-md") . '-' . str_pad(0 + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $generateforwardno = 'FN' . date("Y-md") . '-' . str_pad($latestFN->id + 1, 4, "0", STR_PAD_LEFT);
        }
        if (empty($chk_dl)) {
        } elseif (empty($chk_dl2)) {
        } elseif ($chk_dl != $chk_dl2) {
        } else {
            DB::table('orders')->where('order_no', '=', $order_no)->update(['stat' => 'Forwarded']);
        }
        //dd($chk_dl2);
        return view('manage_order.view_ordered_po', ['generateforwardno' => $generateforwardno, 'chk_dl2' => $chk_dl2, 'chk_dl' => $chk_dl, 'approvedBy' => $approvedBy, 'verifiedBy' => $verifiedBy, 'preparedBy' => $preparedBy, 'order_info' => $order_info, 'dl_noo' => $dl_noo, 'dl_no' => $dl_no, 'dl' => $dl, 'gg' => $gg, 'deliver' => $deliver, 'generatedeliveryno' => $generatedeliveryno, 'user' => $user]);
    }
    public function save_delivery_no(Request $request, $pr_no, $order_no, $sid, $po_no)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|max:255',
            'delivery_no' => 'required|max:255|unique:deliveries',
        ]);
        $max = $request->max_quan;
        $quan = $request->quantity;
        $min = [1];
        // dd(count($request->quantity));
        for ($i = 0; $i < count($request->quantity); $i++) {
            if ($quan[$i] > $max[$i]) {
                session()->flash('error', 'input quantity is greaterthan max value.');
                return response()->json([
                    'status' => 'error',
                    'message' => 'input quantity is greaterthan max value.'
                ], 400);
            }
        }
        for ($i = 0; $i < count($request->quantity); $i++) {
            if ($quan[$i] != $max[$i]) {
                DB::table('orders')->where('order_no', '=', $order_no)->update(['stat' => 'Partially Delivered']);
            }
        }

        // dd($request->all());
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            // $sendto = User::where('position', '=', 'Procurement Officer')
            //     ->get()->first();
            // $pr_no = $request->input('pr_no');
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();
            // $po_no = $request->input('po_no');
            // dd($reqemail);
            $data = [
                'fwd_no' => null,
                'subject' => 'Your Delivery #',
                'dln' => $request->delivery_no,
                'body' => $pr_no,
                'po' => $order_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            // dd($request->iid);
            Mail::to($reqemail->email)->send(new DeliveryNo($data));
            $po_no = $request->po_no;
            $dln = $request->delivery_no;
            $supplier = $request->supplier;
            $status = 'With Delivery No.';
            $quantity = $request->quantity;
            $unit = $request->unit;
            $brand = $request->brand;
            $item_desc = $request->item_desc;
            $iid = $request->iid;
            for ($i = 0; $i < 1; $i++) {
                $datasave = [
                    'po_no' => $po_no,
                    'supplier_id' => $supplier[$i],
                    'delivery_no' => $dln,
                    'order_no' => $order_no,
                    'created_at' => now(),
                ];
                // $pr_item-> save($datasave);
                DB::table('deliveries')->insert($datasave);
            }
            for ($i = 0; $i < count($supplier); $i++) {
                $datasave = [
                    'po_no' => $po_no,
                    'supplier_id' => $supplier[$i],
                    'delivery_no' => $dln,
                    'item_quantity' => $quantity[$i],
                    'item_unit' => $unit[$i],
                    'item_brand' => $brand[$i],
                    'item_desc' => $item_desc[$i],
                    'created_at' => now(),
                ];
                // $pr_item-> save($datasave);
                DB::table('delivery_items')->insert($datasave);
            }
            $delfwd = [
                'delivery_no' => $dln,
                'status' => $status,
                'created_at' => now(),
            ];

            DB::table('del_fwds')->insert($delfwd);
            for ($i = 0; $i < count($supplier); $i++) {
                $datasavee = [
                    'item_id' => $iid[$i],
                    'delivered' => $quantity[$i],
                    'order_no' => $order_no
                ];
                // $pr_item-> save($datasave);
                DB::table('order_item_quantities')->insert($datasavee);
            }
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function deliveries()
    {
        // $delivery = PurchaseOrder::join('orders', 'purchase_orders.po_no', '=', 'orders.po_no')
        //     ->select('*')
        //     ->where('purchase_orders.status', '=', 'Ordered')
        //     ->get();
        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            // ->join('orders', 'purchase_orders.po_no', '=', 'orders.po_no')
            ->select(
                // '*'
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'del_fwds.created_at',
                'del_fwds.status',
                'deliveries.order_no',
                'deliveries.supplier_id',
            )
            ->groupBy(
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'del_fwds.created_at',
                'del_fwds.status',
                'deliveries.order_no',
                'deliveries.supplier_id',
            )
            ->where('del_fwds.status', '=', 'With Delivery No.')
            ->get();
        $send_for_approval = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('forwardeds', 'deliveries.delivery_no', '=', 'forwardeds.delivery_no')
            ->select(
                // '*'
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'del_fwds.forwardedDate',
                'deliveries.order_no',
                'deliveries.supplier_id',
                'forwardeds.forward_no',
            )
            ->whereIn('del_fwds.status', ['Approved', 'For Approval'])
            ->groupBy(
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'del_fwds.forwardedDate',
                'deliveries.order_no',
                'deliveries.supplier_id',
                'forwardeds.forward_no',
            )
            ->get();
        $received = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('orders', 'purchase_orders.po_no', '=', 'orders.po_no')
            ->select(
                // '*'
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                // 'orders.order_no',
                // 'deliveries.supplier_id',
            )
            ->groupBy(
                'purchase_orders.pr_no',
                'deliveries.delivery_no',
                'deliveries.po_no',
                // 'orders.order_no',
                // 'deliveries.supplier_id',
            )
            ->where('del_fwds.status', '=', 'Received')
            ->get();
        $del_received = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('orders', 'deliveries.po_no', '=', 'orders.po_no')
            ->select(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.reqreceivedDate',
                // 'orders.order_no',
                // 'deliveries.supplier_id',
            )
            ->where('del_fwds.status', '=', 'Item Received')
            ->groupBy(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.reqreceivedDate',
                // 'orders.order_no',
                // 'deliveries.supplier_id',
            )
            ->get();

        // dd($send_for_approval);
        return view('manage_delivery.delivery', compact('del_received', 'delivery', 'send_for_approval', 'received'));
    }
    public function view_po_deliveries(Request $request, $pr_no, $po_no, $dln, $order_no, $sid)
    {
        // dd($order_no);
        $order_info = Order::join('order_items', 'orders.pr_no', '=', 'order_items.pr_no')
            ->join('suppliers', 'order_items.supplier_id', '=', 'suppliers.id')
            ->join('purchase_orders', 'orders.pr_no', '=', 'purchase_orders.pr_no')
            // ->join('deliveries', 'purchase_orders.po_no', '=', 'deliveries.po_no')
            ->join('order_item_quantities', 'order_items.id', '=', 'order_item_quantities.item_id')
            ->select(
                'order_items.id',
                'orders.order_no',
                'order_items.item_desc',
                'order_items.brand',
                'order_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'order_items.quantity',
                'order_items.price',
                'order_items.supplier_id',
                // 'order_item_quantities.delivered',
                'suppliers.business_name',
                'orders.payment_term',
                'orders.pr_no',
                'purchase_orders.orderDate',
                'purchase_orders.po_no',
                DB::raw('sum(order_item_quantities.delivered)as delivered'),
                // 'order_items.item_quantity',
                // 'order_items.id as di',
            )
            ->where('orders.pr_no', '=', $pr_no)
            ->where('orders.order_no', '=', $order_no)
            ->where('order_items.supplier_id', '=', $sid)
            ->groupby(
                'order_items.id',
                'orders.order_no',
                'order_items.item_desc',
                'order_items.brand',
                'order_items.unit',
                'suppliers.email',
                'suppliers.contact_no',
                'order_items.quantity',
                'order_items.price',
                'order_items.supplier_id',
                // 'order_item_quantities.delivered',
                'suppliers.business_name',
                'orders.payment_term',
                'orders.pr_no',
                'purchase_orders.orderDate',
                'purchase_orders.po_no',
            )
            ->get()
            // ->unique()
            ->toArray();
        $Collection = collect($order_info);
        $groupCollection = $Collection->groupBy('business_name')->values();
        $gg = collect($groupCollection)->all();

        $dl_no = PurchaseOrder::join('delivery_items', 'purchase_orders.po_no', '=', 'delivery_items.po_no')
            ->join('suppliers', 'delivery_items.supplier_id', '=', 'suppliers.id')
            ->join('orders', 'purchase_orders.po_no', '=', 'orders.po_no')
            ->select(
                '*',
                'delivery_items.delivery_no',
                'delivery_items.created_at',
                'orders.order_no',
            )
            ->where('purchase_orders.pr_no', '=', $pr_no)
            ->where('delivery_items.delivery_no', '=', $dln)
            ->where('delivery_items.supplier_id', '=', $sid)
            ->where('orders.order_no', '=', $order_no)
            ->where('delivery_items.item_quantity', '!=', null)
            ->orderBy('delivery_items.created_at', 'ASC')
            ->get()
            ->toArray();
        $Collection = collect($dl_no);
        $groupCollection = $Collection->groupBy('delivery_no')->values();
        $dl_noo = collect($groupCollection)->all();
        //
        $dl = PurchaseOrder::join('delivery_items', 'purchase_orders.po_no', '=', 'delivery_items.po_no')
            ->join('suppliers', 'delivery_items.supplier_id', '=', 'suppliers.id')
            ->select(
                '*',
                'delivery_items.id',
            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('delivery_items.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first();
        $forwarded = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->select('*')
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()->toArray();

        $latestFN = Forwarded::orderBy('id', 'DESC')->first();
        if ($latestFN == null) {
            $generateforwardno = 'FN-' . date("Y-md") . '-' . str_pad(0 + 1, 4, "0", STR_PAD_LEFT);
        } else {
            $generateforwardno = 'FN-' . date("Y-md") . '-' . str_pad($latestFN->id + 1, 4, "0", STR_PAD_LEFT);
        }
        // dd($gg);
        $prepare = PurchaseOrder::join('staff', 'purchase_orders.preparedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $preparedBy = [];
        foreach ($prepare as $dataa) {
            $preparedBy[] = $dataa;
        }
        $verify = PurchaseOrder::join('staff', 'purchase_orders.verifiedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $verifiedBy = [];
        foreach ($verify as $dataa) {
            $verifiedBy[] = $dataa;
        }
        $approve = PurchaseOrder::join('staff', 'purchase_orders.approvedBy', '=', 'staff.user_id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->where('purchase_orders.pr_no', '=', $pr_no)->get()->toArray();
        $approvedBy = [];
        foreach ($approve as $dataa) {
            $approvedBy[] = $dataa;
        }
        // dd($dl_noo);
        return view('manage_delivery.forward_delivery', ['approvedBy' => $approvedBy, 'verifiedBy' => $verifiedBy, 'preparedBy' => $preparedBy, 'dl' => $dl, 'dl_noo' => $dl_noo, 'dl_no' => $dl_no,  'gg' => $gg, 'forwarded' => $forwarded, 'order_info' => $order_info, 'generateforwardno' => $generateforwardno, 'user' => $user]);
    }
    public function view_deliveries_for_approval(Request $request, $pr_no, $po_no, $dln, $order_no, $sid)
    {
        // dd($dln);
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->select('*')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'del_fwds.status',
                'del_fwds.delivery_no',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'purchase_requests.user_id',
                // 'purchase_requests.created_at',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'deliveries.order_no',
                'del_fwds.forwardedDate',
                'deliveries.supplier_id',
                'users.position',
                'del_fwds.created_at',

            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            // ->where('forwardeds.staff_id', '=', $staff_id)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = User::join('del_fwds', 'users.id', '=', 'del_fwds.isApproved')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            // ->where('users.position', '=', 'ASSD Manager')
            ->get()->toArray();
        $rand = rand(10, 10000);
        $generatedReportNo = 'RN' . date("Y-md") . $rand;
        $report = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->where('reports.staff_id', '=', $id)
            ->get()
            ->toArray();
        $isforwarded = DelFwd::select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->where('del_fwds.isForwarded', '=', '1')
            ->get()->toArray();
        // dd($delivery);
        return view('manage_delivery.forward_for_approval', ['isforwarded' => $isforwarded, 'report' => $report, 'generatedReportNo' => $generatedReportNo, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'forwarded' => $forwarded, 'delivery' => $delivery]);
    }
    public function set_delivery(Request $request, $pr_no, $po_no, $supplier_id)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            'date' => 'required|max:255',
            'deliverby' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $supplier_id = Delivery::select('supplier_id')
                ->where('supplier_id', '=', $supplier_id)
                ->where('po_no', '=', $po_no)->get()->toArray();
            $deliveryDate = $request->input('date');
            $deliverBy = $request->input('deliverby');
            $item_desc = $request->input('item_desc');
            $item_brand = $request->input('item_brand');
            $item_unit = $request->input('item_unit');
            $item_quantity = $request->input('item_quantity');
            $staff_id = $request->input('staff_id');


            // $delivery->update();
            // dd($item);
            DB::table('deliveries')->where('po_no', $po_no)->where('supplier_id', $supplier_id)->update(['deliveryDate' => $deliveryDate, 'deliverBy' => $deliverBy], true);
            DB::table('delivery_items')->where('po_no', $po_no)->where('supplier_id', $supplier_id)->update(['staff_id' => $staff_id, 'item_quantity' => $item_quantity, 'item_unit' => $item_unit, 'item_desc' => $item_desc, 'item_brand' => $item_brand], true);
            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function to_received()
    {
        $id = Auth::user()->id;
        $to_received = Delivery::join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'deliveries.order_no',
                'del_fwds.created_at',

            )
            ->where('purchase_requests.user_id', '=', $id)
            ->whereIn('del_fwds.status',  ['With Delivery No.', 'For Approval', 'Approved'])
            // ->where('del_fwds.status', '=', 'Approved')
            ->groupby(
                'del_fwds.forward_no',
                'del_fwds.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'deliveries.order_no',
                'del_fwds.created_at',
            )
            ->get();
        // dd($to_received);
        $forwarded = Delivery::join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            // ->join('forwardeds', 'deliveries.delivery_no', '=', 'forwardeds.delivery_no')
            ->select(
                // 'forwardeds.forward_no',
                'del_fwds.id',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'del_fwds.created_at',
                // 'forwardeds.staff_id',
            )
            ->where('purchase_requests.user_id', '=', $id)
            ->whereIn('del_fwds.status',  ['With Delivery No.', 'For Approval', 'Approved'])
            ->groupby(
                // 'forwardeds.forward_no',
                'del_fwds.id',
                'deliveries.delivery_no',
                'deliveries.po_no',
                'purchase_requests.user_id',
                'del_fwds.created_at',
                // 'forwardeds.staff_id',
            )
            ->get();
        $fw_approved = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->select(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
            )
            ->where('del_fwds.status', '=', 'Approved')
            ->get()
            ->unique();
        $item_received = Forwarded::join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->select(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.reqreceivedDate',
                'deliveries.order_no',

            )
            ->where('del_fwds.status', '=', 'Item Received')
            ->where('purchase_requests.user_id', '=', $id)
            ->groupby(
                'forwardeds.forward_no',
                'forwardeds.delivery_no',
                'deliveries.po_no',
                'forwardeds.staff_id',
                'del_fwds.reqreceivedDate',
                'deliveries.order_no',
            )
            ->get();
        $reported = Report::join('deliveries', 'reports.delivery_no', '=', 'deliveries.delivery_no')->select('*')
            ->where('staff_id', '=', $id)
            ->get();
        // dd($id);
        return view('manage_forwarded.forwarded', compact('reported', 'item_received', 'to_received', 'forwarded', 'fw_approved'));
    }
    public function view_po_to_received(Request $request, $dln, $po_no, $user_id)
    {
        // dd($);
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->select('*', 'forwarded_items.id')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'del_fwds.status',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'purchase_requests.created_at',
                'staff.user_id',
                'del_fwds.delivery_no',
                'del_fwds.forwardedDate',
                'deliveries.order_no',
                'deliveries.supplier_id',
                'users.position',

            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'staff.user_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            // ->where('forwardeds.staff_id', '=', $user_id)
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = User::join('del_fwds', 'users.id', '=', 'del_fwds.isApproved')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $rand = rand(10, 10000);
        $generatedReportNo = 'RN' . date("Y-md") . $rand;
        $report = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->where('reports.staff_id', '=', $id)
            ->get()
            ->toArray();
        // dd($delivery);
        return view('manage_forwarded.view_forwarded', ['forwarded' => $forwarded, 'report' => $report, 'generatedReportNo' => $generatedReportNo, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'delivery' => $delivery]);
    }
    public function forward_delivery(Request $request, $pr_no, $po_no, $supplier_id)
    {
        // dd($request->order);
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'ASSD Manager')
                ->get()->first();
            $reqemail = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
                ->select('users.email')
                ->where('purchase_requests.pr_no', '=', $pr_no)->get()->first();

            $forward_no = $request->input('forward_no');
            $dln = $request->input('dln');
            $staff_id = Auth::user()->id;
            $dln_pg = $request->input('dln_pg');
            if (count($dln_pg) == 1) {
                $data = [
                    'forward_no' => $forward_no,
                    'delivery_no' => $dln,
                    'staff_id' => $staff_id
                ];
                DB::table('forwardeds')->insert($data);
            } else {
                for ($i = 0; $i < count($dln_pg); $i++) {
                    $data = [
                        'forward_no' => $forward_no,
                        'delivery_no' => $dln[$i],
                        'staff_id' => $staff_id
                    ];
                    DB::table('forwardeds')->insert($data);
                }
            }
            for ($i = 0; $i < count($dln_pg); $i++) {
                $data = [
                    'status' => 'For Approval',
                    'isForwarded' => '1',
                    'forwardedDate' => now()
                ];
                DB::table('del_fwds')->where('delivery_no', $dln_pg[$i])->update($data);
            }

            $quantity = $request->input('item_quantity');
            $unit = $request->input('item_unit');
            $brand = $request->input('item_brand');
            $item_desc = $request->input('item_desc');
            for ($i = 0; $i < count($item_desc); $i++) {
                $datasave = [
                    'forward_no' => $forward_no,
                    'item_desc' => $item_desc[$i],
                    'item_quantity' => $quantity[$i],
                    'item_unit' => $unit[$i],
                    'item_brand' => $brand[$i],

                ];
                DB::table('forwarded_items')->insert($datasave);
            }

            if ($request->order != null) {
                DB::table('orders')->where('order_no', $request->order)->update(['stat' => 'Forwarded']);
            }
            $data = [
                'subject' => 'Purchase Order has been forwarded to ASSD to be approved',
                'fwd_no' => $forward_no,
                'dln' => implode(", ", $dln_pg),
                'body' => $pr_no,
                'po' => $po_no,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            Mail::to($sendto->email)->send(new Transmital($data));
            Mail::to($reqemail->email)->send(new DeliveryNo($data));
            // }
            return response()->json([
                'success' => 'Supplier has been Selected'
            ]);
        }
    }
    public function received_delivery(Request $request, $pr_no, $po_no, $supplier_id)
    {
        dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'date' => 'required|max:255',
            // 'deliverby' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $dln = $request->input('dln');
            $received = 'Received';

            DB::table('del_fwds')->where('delivery_no', $dln)->update(['status' => $received, 'isReceived' => '1', 'receivedDate' => now()], true);


            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function view_denied_pr(Request $request, $pr_no)
    {
        $pr_info = PurchaseRequest::join('purchase_request_items', 'purchase_requests.pr_no', '=', 'purchase_request_items.pr_no')
            ->join('departments', 'purchase_requests.department_id', '=', 'departments.id')
            ->join('buildings', 'departments.building_id', '=', 'buildings.id')
            ->join('purchase_request_histories', 'purchase_requests.pr_no', '=', 'purchase_request_histories.pr_no')
            ->select('*')
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $pr_infos = PurchaseRequest::where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        $output = [];
        foreach ($pr_info as $data) {
            $output[] = $data;
        }
        $outputs = [];
        foreach ($pr_infos as $dataa) {
            $outputs[] = $dataa;
        }
        $deny_pr = DeniedRequest::where('denied_requests.pr_no', '=', $pr_no)->get()->toArray();
        $deny = [];
        foreach ($deny_pr as $dataa) {
            $deny[] = $dataa;
        }
        $user_email = User::join('purchase_requests', 'users.id', '=', 'purchase_requests.user_id')
            ->select(
                'users.email',
            )
            ->where('purchase_requests.pr_no', '=', $pr_no)->get()->toArray();
        // dd($outputs);
        return view('manage_purchase_request.view_denied_pr', ['pr_infos' => $pr_infos, 'user_email' => $user_email, 'deny_pr' => $deny_pr, 'output' => $output, 'outputs' => $outputs]);
    }
    public function view_item_received(Request $request, $fwn, $dln, $po_no, $staff_id)
    {
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->select('*')
            ->where('forwarded_items.forward_no', '=', $fwn)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')

            ->select(
                'del_fwds.status',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            ->where('forwardeds.staff_id', '=', $staff_id)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = User::join('del_fwds', 'users.id', '=', 'del_fwds.isApproved')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select('*')
            ->get()->toArray();
        // dd($delivery);
        return view('manage_forwarded.view_forwarded', ['isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'forwarded' => $forwarded, 'delivery' => $delivery]);
    }
    public function received_item(Request $request, $dln, $po_no,)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            // 'date' => 'required|max:255',
            // 'deliverby' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Procurement Officer')
                ->get()->first();
            // $pr_no = $request->input('pr_no');
            $pr = PurchaseOrder::select(
                'pr_no'
            )
                ->where('po_no', '=', $request->input('po_no'))->get()->first();
            // dd($reqemail);
            $po_noo = $request->input('po_no');
            $data = [
                'subject' => 'Item Received',
                'body' => $pr->pr_no,
                'fwd_no' => null,
                'po' => $po_noo,
                'dln' => $dln,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            try {
                $user = $request->input('staff_id');
                DB::table('del_fwds')->where('delivery_no', $dln)->update(['status' => 'Item Received', 'isReqReceived' => $user, 'reqreceivedDate' => now()]);
                Mail::to($sendto->email)->send(new Transmital($data));
                // Mail::to($reqemail->email)->send(new DeliveryNo($data));
                return response()->json([
                    'success' => 'Supplier has been Selected'
                ]);
            } catch (Exception $e) {
                return response()->json(['Sorry! Please try again latter']);
            }
        }
    }
    public function track_po_to_received(Request $request, $dln, $po_no, $user_id)
    {
        // $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
        //     ->select('*')
        //     ->where('forwarded_items.forward_no', '=', $fwn)
        //     ->get()
        //     ->unique()
        //     ->toArray();
        $forwarded = Forwarded::join('forwarded_items', 'forwardeds.forward_no', '=', 'forwarded_items.forward_no')
            ->join('deliveries', 'forwardeds.delivery_no', '=', 'deliveries.delivery_no')
            ->select('*')
            ->where('forwardeds.delivery_no', '=', $dln)
            ->get()
            ->unique()
            ->toArray();

        $delivery = Delivery::join('purchase_orders', 'deliveries.po_no', '=', 'purchase_orders.po_no')
            ->join('suppliers', 'deliveries.supplier_id', '=', 'suppliers.id')
            ->join('purchase_requests', 'purchase_orders.pr_no', '=', 'purchase_requests.pr_no')
            ->join('staff', 'purchase_requests.user_id', '=', 'staff.user_id')
            ->join('del_fwds', 'deliveries.delivery_no', '=', 'del_fwds.delivery_no')
            ->join('departments', 'staff.department_id', '=', 'departments.id')
            ->select(
                'del_fwds.status',
                'purchase_orders.po_no',
                'purchase_requests.pr_no',
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'departments.Dept_name',
                'purchase_requests.purpose',
                'del_fwds.isApproved',
                'del_fwds.isReqReceived',
                'deliveries.delivery_no'

            )
            ->where('purchase_orders.po_no', '=', $po_no)
            ->where('deliveries.delivery_no', '=', $dln)
            ->get()
            ->toArray();
        $transmittedby = Forwarded::join('staff', 'forwardeds.staff_id', '=', 'staff.id')
            ->join('users', 'forwardeds.staff_id', '=', 'users.id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'staff.id'
            )
            ->where('forwardeds.staff_id', '=', Auth::user()->id)
            ->get()
            ->toArray();
        $id = Auth::user()->id;
        $user = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->join('staff', 'users.id', '=', 'staff.user_id')
            ->select(
                'staff.fname',
                'staff.mname',
                'staff.lname',
                'users.position',
                'roles.display_name',
                'users.id'
            )
            ->where('users.id', '=', $id)
            ->first()->toArray();
        $isApproved = DelFwd::select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $isApprovedBy = DelFwd::join('staff', 'del_fwds.isApproved', '=', 'staff.user_id')
            ->select('*')
            ->where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $del = DelFwd::where('del_fwds.delivery_no', '=', $dln)
            ->get()->toArray();
        $delby = Staff::join('users', 'staff.id', '=', 'users.id')
            ->where('users.position', '=', 'Procurement Officer')
            ->get()->toArray();
        // dd($dln);
        return view('manage_forwarded.track_forwarded', ['delby' => $delby, 'del' => $del, 'isApprovedBy' => $isApprovedBy, 'isApproved' => $isApproved, 'user' => $user, 'transmittedby' => $transmittedby, 'forwarded' => $forwarded, 'delivery' => $delivery]);
    }
    public function report_item(Request $request, $dln)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'FID' => ['unique:report_items'],
        ], [
            'FID.unique' => 'Already Reported'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {
            $email = Auth::user()->email;
            $role = User::join('staff', 'users.id', '=', 'staff.user_id')
                ->select('staff.fname', 'staff.lname')
                ->where('users.id', '=', Auth::user()->id)
                ->get()->first();
            $sendto = User::where('position', '=', 'Procurement Officer')
                ->get()->first();
            $data = [
                'subject' => 'Report Item - ',
                'body' => $request->input('rn'),
                // 'po' => $po_noo,
                // 'dln' => $dln,
                'from' => $email,
                'name' => $role->fname . ' ' . $role->lname,
            ];
            $id = Auth::user()->id;
            $report = new Report;
            $report->Report_no = $request->input('rn');
            $report->delivery_no = $request->input('dln');
            $report->staff_id = $id;
            $report->save();

            for ($i = 0; $i < count($request->report_chk); $i++) {
                $datasave = [
                    'report_no' => $request->input('rn'),
                    'FID' => $request->input('report_chk')[$i],
                    'item_status' => $request->input('report_message')[$i],
                    'quantity' => $request->input('report_quantity')[$i],
                ];
                // dd($datasave);
                DB::table('report_items')->insert($datasave);
            }

            Mail::to($sendto->email)->send(new ReportItemm($data));

            return response()->json([
                'success' => 'item updated successfully'
            ]);
        }
    }
    public function view_reported_item(Request $request, $rn)
    {
        $id = Auth::user()->id;
        // dd($id);
        $reported = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->join('forwarded_items', 'report_items.FID', '=', 'forwarded_items.id')
            ->join('staff', 'reports.staff_id', '=', 'staff.id')
            ->select('*', 'reports.created_at')
            ->where('reports.Report_no', '=', $rn)
            ->where('reports.staff_id', '=', $id)
            ->get()
            // ->unique()
            ->toArray();
        // dd($reported);
        return view('manage_forwarded.view_reported_item', ['reported' => $reported]);
    }
    public function reported_items(Request $request)
    {
        $id = Auth::user()->id;
        $reported = Report::select('*', 'reports.created_at')
            ->get()
            ->unique()
            ->toArray();
        // dd($reported);
        return view('manage_forwarded.reported_items', ['reported' => $reported]);
    }
    public function view_reported_item2(Request $request, $rn)
    {
        $id = Auth::user()->id;
        $reported = Report::join('report_items', 'reports.Report_no', '=', 'report_items.report_no')
            ->join('forwarded_items', 'report_items.FID', '=', 'forwarded_items.id')
            ->join('staff', 'reports.staff_id', '=', 'staff.id')
            ->select('*', 'reports.created_at')
            ->where('reports.Report_no', '=', $rn)
            ->get()
            // ->unique()
            ->toArray();
        // dd($reported);
        return view('manage_forwarded.view_reported_item', ['reported' => $reported]);
    }
    public function update_delivery(Request $request, $po_no)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), []);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
            ]);
        } else {

            for ($i = 0; $i < count($request->id); $i++) {
                $datasave = [
                    'item_quantity' => $request->item_quantity[$i],
                    'updated_at' => now(),
                ];
                // dd($request->id);
                DB::table('delivery_items')->where('id', $request->id[$i])->update($datasave, 'item_quantity');
            }
            return response()->json([
                'success' => 'quantity updated successfully'
            ]);
        }
    }
}
