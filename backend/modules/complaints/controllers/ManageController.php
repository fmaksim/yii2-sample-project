<?php

namespace backend\modules\complaints\controllers;

use common\components\storage\Storage;
use frontend\components\ComplaintService;
use Yii;
use backend\models\Post;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ManageController implements the CRUD actions for Post model.
 */
class ManageController extends Controller
{

    protected $fileStorage;
    protected $complaintService;

    public function __construct(
        $id,
        $module,
        Storage $fileStorage,
        ComplaintService $complaintService,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->fileStorage = $fileStorage;
        $this->complaintService = $complaintService;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Post::findComplaints(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'fileStorage' => $this->fileStorage,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Approve an existing complaint at Post model.
     * If approving is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionApprove($id)
    {

        try {
            $post = $this->findModel($id);
            if ($this->complaintService->approveComplaint($post)) {
                Yii::$app->session->setFlash("success", "Complaint was successfully approved!");
            } else {
                Yii::$app->session->setFlash("error", "Approving error!");
            }
            return $this->redirect(['index']);
        } catch (\Exception $e) {
            //var_dump($e->getTraceAsString());
            Yii::$app->session->setFlash("error", $e->getTraceAsString());
            return $this->redirect(['index']);
        }

    }
}
