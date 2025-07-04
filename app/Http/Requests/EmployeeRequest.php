<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * EmployeeRequest（従業員バリデーション用FormRequest）
 * -----------------------------------------------------
 * - 従業員の追加・編集時のバリデーションを一元管理
 * - コントローラから分離することで保守性・再利用性向上
 * - Laravelのバリデーション共通化の基本例
 *
 * 【使い方】
 * - コントローラのstore/updateで型指定するだけで自動適用
 *   例: public function store(EmployeeRequest $request)
 */
class EmployeeRequest extends FormRequest
{
    /**
     * 認可ロジック（今回は全て許可）
     */
    public function authorize()
    {
        return true;
    }

    /**
     * バリデーションルール
     * - 新規登録時と更新時でuniqueルールを自動切り替え
     */
    public function rules()
    {
        // idがルートパラメータにあれば「更新」、なければ「新規登録」
        $id = $this->route('id');
        return [
            // 更新時は自分自身のemployee_idを除外
            'employee_id' => [
                'required',
                'unique:employees,employee_id' . ($id ? ",{$id}" : ''),
            ],
            'last_name' => 'required',
            'first_name' => 'required',
            'joined_at' => 'required|date',
        ];
    }
}
