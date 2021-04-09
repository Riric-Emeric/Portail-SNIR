#include <opencv2/opencv.hpp>
#include <iostream>
#include <zbar.h>

using namespace cv;
using namespace std;
using namespace zbar;

int main()
{
    Mat fichier = imread("/home/snir/Emeric/images/QR-Code.jpg", IMREAD_GRAYSCALE);
    //imshow("Image", fichier);
    waitKey();

    int largeur = fichier.cols;
    int hauteur = fichier.rows;

    ImageScanner decodage;
    decodage.set_config(ZBAR_NONE, ZBAR_CFG_ENABLE, 1);

    Image image(largeur, hauteur, "Y800", fichier.data, largeur*hauteur);
    //cout << decodage.scan(image) << endl;
    decodage.scan(image);

    for (auto symbole = image.symbol_begin(); symbole != image.symbol_end(); ++symbole)
    {
        cout << "Type : " << symbole->get_type_name() << endl;
        cout << "Data : " << symbole->get_data() << endl << endl;
    }
    return 0;
}
